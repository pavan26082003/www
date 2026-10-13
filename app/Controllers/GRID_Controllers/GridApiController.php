<?php

namespace App\Controllers\GRID_Controllers;

use CodeIgniter\API\ResponseTrait;

/**
 * GridApiController
 * High-performance RESTful API Controller for GRID Professional Platform.
 * 
 * Strict MVC Architecture:
 * - Controller handles HTTP request validation, session auth, and JSON responses.
 * - All database persistence and queries are encapsulated inside App\Models\GRID_MODEL models:
 *     - Grid_profile_model
 *     - Grid_skills_model
 *     - Grid_connections_model
 *     - Grid_jobs_model
 */
class GridApiController extends \App\Controllers\Security_Controller
{
    use ResponseTrait;

    protected $profileModel;
    protected $skillsModel;
    protected $connectionsModel;
    protected $jobsModel;
    protected $messagesModel;
    protected $postsModel;
    protected $challengesModel;
    protected $sectionsModel;

    public function __construct()
    {
        parent::__construct(false);

        // Instantiate encapsulated MVC models
        $this->profileModel     = model('App\Models\GRID_MODEL\Grid_profile_model');
        $this->skillsModel      = model('App\Models\GRID_MODEL\Grid_skills_model');
        $this->connectionsModel = model('App\Models\GRID_MODEL\Grid_connections_model');
        $this->jobsModel        = model('App\Models\GRID_MODEL\Grid_jobs_model');
        $this->messagesModel    = model('App\Models\GRID_MODEL\Grid_messages_model');
        $this->postsModel       = model('App\Models\GRID_MODEL\Grid_posts_model');
        $this->challengesModel  = model('App\Models\GRID_MODEL\Grid_challenges_model');
        $this->sectionsModel    = model('App\Models\GRID_MODEL\Grid_sections_model');
    }

    /**
     * Resolve current user ID safely from session
     */
    protected function getAuthUserId(): int
    {
        if (isset($this->login_user) && !empty($this->login_user->id)) {
            return (int) $this->login_user->id;
        }
        $session = \Config\Services::session();
        if ($session->has('user_id') && !empty($session->get('user_id'))) {
            return (int) $session->get('user_id');
        }
        return 1; // Default fallback for local testing
    }

    /**
     * GET /api/me
     * Minimal session-based identity endpoint for the Main Hub super-app
     * handoff: GRID has no login of its own, so this is how it recognizes
     * who's arriving. Strictly session-derived — never accepts user_id from
     * the frontend — and deliberately does NOT use getAuthUserId()'s
     * fallback-to-1 (that exists only for this app's own CLI testing
     * convenience; this endpoint must return a real 401 when there's no
     * authenticated session). Returns only id/grid_id/first_name/last_name
     * from the real `users` table for an active, non-deleted account.
     */
    public function me()
    {
        $userId = null;
        if (isset($this->login_user) && !empty($this->login_user->id)) {
            $userId = (int) $this->login_user->id;
        } else {
            $session = \Config\Services::session();
            if ($session->has('user_id') && !empty($session->get('user_id'))) {
                $userId = (int) $session->get('user_id');
            }
        }

        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $user = db_connect('default')->table('users')
            ->select('id, grid_id, first_name, last_name')
            ->where('id', $userId)
            ->where('status', 'active')
            ->where('deleted', 0)
            ->get()
            ->getRow();

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'message' => 'User not found.'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data' => [
                'user_id' => (int) $user->id,
                'grid_id' => $user->grid_id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name
            ]
        ]);
    }

    /**
     * Resolve raw users.image (which may be a PHP serialized array or bare filename)
     * into a valid, public HTTP URL.
     */
    protected function resolveAvatarUrl($image): string
    {
        if (empty($image)) {
            return '';
        }
        if (function_exists('get_avatar')) {
            return (string) get_avatar($image);
        }
        $file = @unserialize($image);
        if (is_array($file) && !empty($file['file_name'])) {
            return base_url('files/profile_images/' . $file['file_name']);
        }
        if (is_string($image) && preg_match('/^_file[a-z0-9]+.*\.png$/i', $image)) {
            return base_url('files/profile_images/' . $image);
        }
        return (string) $image;
    }

    // =========================================================================
    // 1. SKILLS AUTOCOMPLETE & TAXONOMY API
    // =========================================================================

    /**
     * GET /grid/api/skills/suggest?q=py
     */
    public function suggestSkills()
    {
        $q = trim((string) $this->request->getGet('q'));
        $skills = $this->skillsModel->suggest_skills($q, 15);

        return $this->response->setJSON([
            'success' => true,
            'data' => $skills
        ]);
    }

    /**
     * GET /grid/api/search/users?q=name
     * Header search bar: find people by name.
     */
    public function searchUsers()
    {
        $currentUserId = $this->getAuthUserId();
        $q = trim((string) $this->request->getGet('q'));

        $results = $this->profileModel->search_users($q, 8, $currentUserId);

        foreach ($results as &$r) {
            $r['avatar'] = $this->resolveAvatarUrl($r['avatar'] ?? '');
        }
        unset($r);

        return $this->response->setJSON([
            'success' => true,
            'data' => $results
        ]);
    }

    // =========================================================================
    // 2. PROFILE & IDENTITY API
    // =========================================================================

    /**
     * GET /grid/api/profile/{targetIdentifier?}
     * Accepts either a numeric user_id or a string grid_id vanity handle (e.g. pavan-kumar-8421).
     */
    public function getProfile($targetIdentifier = null)
    {
        $currentUserId = $this->getAuthUserId();

        if ($targetIdentifier !== null && !ctype_digit((string) $targetIdentifier)) {
            $ownerProfile = $this->profileModel->get_profile_by_grid_id($targetIdentifier);
            if (!$ownerProfile || empty($ownerProfile['user_id'])) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Profile not found.'
                ]);
            }
            $userId = (int) $ownerProfile['user_id'];
        } else {
            $userId = $targetIdentifier ? (int) $targetIdentifier : $currentUserId;
        }

        $isOwner = ($userId === $currentUserId);

        // Fetch User Basic Info via standard Users_model
        $user = $this->Users_model->get_one($userId);
        if (!$user || empty($user->id)) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }

        // Fetch Profile, Skills, and Relationship through models
        $profile = $this->profileModel->get_profile_by_user_id($userId);
        $skills = $this->skillsModel->get_user_skills($userId);
        $relationship = $isOwner ? 'self' : $this->connectionsModel->get_connection_status($currentUserId, $userId);

        // Fetch 1-to-Many Relational Profile Sections
        $experiences = $this->sectionsModel->get_user_experiences($userId);
        $education = $this->sectionsModel->get_user_educations($userId);
        $certifications = $this->sectionsModel->get_user_certifications($userId);
        $projects = $this->sectionsModel->get_user_projects($userId);
        $honors = $this->sectionsModel->get_user_honors($userId);
        $languages = $this->sectionsModel->get_user_languages($userId);
        $artifacts = $this->sectionsModel->get_user_artifacts($userId);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'isOwner' => $isOwner,
                'user' => [
                    'id' => (int) $user->id,
                    'name' => trim($user->first_name . ' ' . ($user->last_name ?? '')),
                    'avatar' => $this->resolveAvatarUrl($profile['avatar'] ?? ''),
                    'initials' => strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name ?? '', 0, 1))
                ],
                'profile' => $profile,
                'skills' => $skills,
                'experiences' => $experiences,
                'education' => $education,
                'certifications' => $certifications,
                'projects' => $projects,
                'honors' => $honors,
                'languages' => $languages,
                'artifacts' => $artifacts,
                'relationship' => $relationship
            ]
        ]);
    }

    /**
     * POST /grid/api/profile/save
     * High-resilience endpoint for partial or full profile updates.
     * Only updates keys explicitly supplied in payload to prevent wiping existing data.
     */
    public function saveProfile()
    {
        $userId = $this->getAuthUserId();
        $payload = $this->request->getJSON(true) ?: $this->request->getPost();

        if (empty($payload)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'No changes provided.'
            ]);
        }

        $data = [];

        if (array_key_exists('headline', $payload)) {
            $data['headline'] = substr(trim((string)$payload['headline']), 0, 255);
            // Sync to factor_db2.users.job_title
            $this->Users_model->update_where(
                ['job_title' => $data['headline']],
                ['id' => $userId]
            );
        }

        if (array_key_exists('pronoun', $payload)) {
            $data['pronoun'] = substr(trim((string)$payload['pronoun']), 0, 32);
        }

        if (array_key_exists('bio', $payload)) {
            $data['bio'] = trim((string)$payload['bio']);
        }

        if (array_key_exists('about_skills', $payload)) {
            $val = $payload['about_skills'];
            $data['about_skills'] = is_array($val) ? implode(', ', $val) : trim((string)$val);
        }

        if (array_key_exists('about_json', $payload) || array_key_exists('about', $payload)) {
            $val = $payload['about_json'] ?? $payload['about'];
            $data['about_json'] = is_string($val) ? $val : json_encode($val);
            if (is_array($val)) {
                if (isset($val['text']) && !isset($data['bio'])) {
                    $data['bio'] = trim((string)$val['text']);
                }
                if (isset($val['skills']) && !isset($data['about_skills'])) {
                    $data['about_skills'] = is_array($val['skills']) ? implode(', ', $val['skills']) : trim((string)$val['skills']);
                }
            }
        }

        if (array_key_exists('role', $payload) && in_array($payload['role'], ['candidate', 'recruiter'], true)) {
            $data['role'] = $payload['role'];
        }

        if (array_key_exists('is_open_to_work', $payload)) {
            $data['is_open_to_work'] = !empty($payload['is_open_to_work']) ? 1 : 0;
        }

        if (array_key_exists('roles', $payload) || array_key_exists('otw_roles', $payload) || array_key_exists('open_to_work_roles', $payload)) {
            $val = $payload['roles'] ?? ($payload['otw_roles'] ?? $payload['open_to_work_roles']);
            $data['otw_roles'] = substr(trim((string)$val), 0, 255);
        }

        if (array_key_exists('note', $payload) || array_key_exists('otw_note', $payload) || array_key_exists('open_to_work_note', $payload)) {
            $val = $payload['note'] ?? ($payload['otw_note'] ?? $payload['open_to_work_note']);
            $data['otw_note'] = substr(trim((string)$val), 0, 255);
        }

        if (array_key_exists('is_hiring', $payload)) {
            $data['is_hiring'] = !empty($payload['is_hiring']) ? 1 : 0;
        }

        if (array_key_exists('location', $payload)) {
            $data['location'] = substr(trim((string)$payload['location']), 0, 128);
        }

        if (array_key_exists('website', $payload)) {
            $data['website'] = substr(trim((string)$payload['website']), 0, 255);
        }

        if (array_key_exists('github_url', $payload)) {
            $data['github_url'] = substr(trim((string)$payload['github_url']), 0, 255);
        }

        if (array_key_exists('linkedin_url', $payload)) {
            $data['linkedin_url'] = substr(trim((string)$payload['linkedin_url']), 0, 255);
        }

        // Full Name support (syncs directly to factor_db2.users)
        $fullName = $payload['full_name'] ?? $payload['fullName'] ?? $payload['name'] ?? null;
        if ($fullName !== null) {
            $cleanName = trim((string)$fullName);
            $parts = explode(' ', $cleanName, 2);
            $firstName = $parts[0] ?? '';
            $lastName = $parts[1] ?? '';
            $this->Users_model->update_where(
                ['first_name' => $firstName, 'last_name' => $lastName],
                ['id' => $userId]
            );
        }

        // Dedicated JSON columns for rich portfolio sections
        if (array_key_exists('honors', $payload) || array_key_exists('honors_json', $payload)) {
            $val = $payload['honors_json'] ?? $payload['honors'];
            $data['honors_json'] = is_string($val) ? $val : json_encode($val);
        }

        if (array_key_exists('languages', $payload) || array_key_exists('languages_json', $payload)) {
            $val = $payload['languages_json'] ?? $payload['languages'];
            $data['languages_json'] = is_string($val) ? $val : json_encode($val);
        }

        if (array_key_exists('experiences', $payload) || array_key_exists('experience_json', $payload)) {
            $val = $payload['experience_json'] ?? $payload['experiences'];
            $data['experience_json'] = is_string($val) ? $val : json_encode($val);
        }

        if (array_key_exists('education', $payload) || array_key_exists('education_json', $payload)) {
            $val = $payload['education_json'] ?? $payload['education'];
            $data['education_json'] = is_string($val) ? $val : json_encode($val);
        }

        if (array_key_exists('projects', $payload) || array_key_exists('projects_json', $payload)) {
            $val = $payload['projects_json'] ?? $payload['projects'];
            $data['projects_json'] = is_string($val) ? $val : json_encode($val);
        }

        if (array_key_exists('artifacts', $payload) || array_key_exists('artifacts_json', $payload)) {
            $val = $payload['artifacts_json'] ?? $payload['artifacts'];
            $data['artifacts_json'] = is_string($val) ? $val : json_encode($val);
        }

        // Cover Banner attributes persistence
        if (array_key_exists('banner_type', $payload) || array_key_exists('bannerType', $payload)) {
            $val = $payload['banner_type'] ?? $payload['bannerType'];
            $data['banner_type'] = in_array($val, ['gradient', 'video', 'image'], true) ? $val : 'gradient';
        }

        if (array_key_exists('banner_video_src', $payload) || array_key_exists('bannerVideoSrc', $payload)) {
            $data['banner_video_src'] = trim((string)($payload['banner_video_src'] ?? $payload['bannerVideoSrc']));
        }

        if (array_key_exists('banner_image_src', $payload) || array_key_exists('bannerImageSrc', $payload)) {
            $data['banner_image_src'] = trim((string)($payload['banner_image_src'] ?? $payload['bannerImageSrc']));
        }

        if (array_key_exists('banner_gradient', $payload) || array_key_exists('bannerGradient', $payload)) {
            $data['banner_gradient'] = trim((string)($payload['banner_gradient'] ?? $payload['bannerGradient']));
        }

        if (array_key_exists('banner_tag', $payload) || array_key_exists('bannerTag', $payload)) {
            $data['banner_tag'] = substr(trim((string)($payload['banner_tag'] ?? $payload['bannerTag'])), 0, 64);
        }

        if (array_key_exists('banner_title', $payload) || array_key_exists('bannerTitle', $payload)) {
            $data['banner_title'] = substr(trim((string)($payload['banner_title'] ?? $payload['bannerTitle'])), 0, 128);
        }

        if (array_key_exists('banner_url', $payload) || array_key_exists('bannerUrl', $payload)) {
            $data['banner_url'] = substr(trim((string)($payload['banner_url'] ?? $payload['bannerUrl'])), 0, 255);
        }

        if (!empty($data)) {
            $this->profileModel->save_profile($userId, $data);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Profile updated successfully! ✨',
            'data' => $data
        ]);
    }

    /**
     * POST /grid/api/profile/section/save
     * Persist an item in a 1-to-many relational table:
     * (grid_experiences, grid_education, grid_certifications, grid_projects, grid_honors, grid_languages, grid_artifacts)
     */
    public function saveSectionItem()
    {
        $userId = $this->getAuthUserId();
        $payload = $this->request->getJSON(true) ?: $this->request->getPost();

        $section = strtolower(trim((string)($payload['section'] ?? '')));
        $item = (array)($payload['item'] ?? $payload);
        $itemId = $payload['id'] ?? ($item['id'] ?? null);

        $savedId = null;

        switch ($section) {
            case 'experience':
            case 'experiences':
                $savedId = $this->sectionsModel->save_user_experience($userId, $item, $itemId);
                break;
            case 'education':
            case 'educations':
                $savedId = $this->sectionsModel->save_user_education($userId, $item, $itemId);
                break;
            case 'certification':
            case 'certifications':
                $savedId = $this->sectionsModel->save_user_certification($userId, $item, $itemId);
                break;
            case 'project':
            case 'projects':
                $savedId = $this->sectionsModel->save_user_project($userId, $item, $itemId);
                break;
            case 'honor':
            case 'honors':
                $savedId = $this->sectionsModel->save_user_honor($userId, $item, $itemId);
                break;
            case 'language':
            case 'languages':
                $savedId = $this->sectionsModel->save_user_language($userId, $item, $itemId);
                break;
            case 'artifact':
            case 'artifacts':
                $savedId = $this->sectionsModel->save_user_artifact($userId, $item, $itemId);
                break;
            case 'skill':
            case 'skills':
                $skillName = trim($item['name'] ?? '');
                if (!empty($skillName)) {
                    $this->skillsModel->associate_user_skills($userId, [$skillName]);
                    $row = $this->skillsModel->db->table('grid_skills')->select('id')->where('name', $skillName)->get()->getRowArray();
                    $savedId = $row ? (int)$row['id'] : null;
                }
                break;
            default:
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => "Unknown section: {$section}"
                ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => ucfirst($section) . ' saved successfully! ✨',
            'id' => $savedId
        ]);
    }

    /**
     * POST /grid/api/profile/section/delete
     * Remove an item from a 1-to-many relational profile table
     */
    public function deleteSectionItem()
    {
        $userId = $this->getAuthUserId();
        $payload = $this->request->getJSON(true) ?: $this->request->getPost();

        $section = strtolower(trim((string)($payload['section'] ?? '')));
        $itemId = $payload['id'] ?? ($payload['item']['id'] ?? null);

        if (!$itemId) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Valid item ID is required.'
            ]);
        }

        switch ($section) {
            case 'experience':
            case 'experiences':
                $this->sectionsModel->delete_user_experience($userId, $itemId);
                break;
            case 'education':
            case 'educations':
                $this->sectionsModel->delete_user_education($userId, $itemId);
                break;
            case 'certification':
            case 'certifications':
                $this->sectionsModel->delete_user_certification($userId, $itemId);
                break;
            case 'project':
            case 'projects':
                $this->sectionsModel->delete_user_project($userId, $itemId);
                break;
            case 'honor':
            case 'honors':
                $this->sectionsModel->delete_user_honor($userId, $itemId);
                break;
            case 'language':
            case 'languages':
                $this->sectionsModel->delete_user_language($userId, $itemId);
                break;
            case 'artifact':
            case 'artifacts':
                $this->sectionsModel->delete_user_artifact($userId, $itemId);
                break;
            case 'skill':
            case 'skills':
                $this->skillsModel->remove_user_skill($userId, $itemId);
                break;
            default:
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => "Unknown section: {$section}"
                ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => ucfirst($section) . ' removed successfully.'
        ]);
    }

    /**
     * POST /grid/api/profile/upload-avatar
     * Dedicated avatar upload for the authenticated user.
     * Saves into files/profile_images/ and saves the filename into users.image.
     */
    public function uploadAvatar()
    {
        $userId = $this->getAuthUserId();
        $file = $this->request->getFile('avatar') ?: $this->request->getFile('file') ?: $this->request->getFile('image');

        if (!$file || !$file->isValid()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'No valid image file uploaded.'
            ]);
        }

        $imageMimes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp', 'image/gif'];
        $mime = $file->getMimeType();
        if (!in_array($mime, $imageMimes, true)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid image type. Please upload a JPG, PNG, or WebP photo.'
            ]);
        }

        $targetDir = FCPATH . 'files/profile_images/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $ext = strtolower($file->guessExtension() ?: $file->getExtension() ?: 'jpg');
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }

        $newName = '_file' . bin2hex(random_bytes(6)) . '_' . time() . '.' . $ext;
        $file->move($targetDir, $newName);

        // Update grid_db.grid_profiles strictly (isolated from factor_db2.users)
        $this->profileModel->save_profile($userId, ['avatar' => $newName]);

        $avatarUrl = base_url('files/profile_images/' . $newName);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Profile photo updated successfully! 📸',
            'avatar_url' => $avatarUrl,
            'file_name' => $newName
        ]);
    }

    /**
     * POST /grid/api/profile/upload-banner
     * Upload banner media (image or video) for the authenticated user's profile.
     * Saves into files/profile_banners/ and returns the absolute public URL.
     */
    public function uploadBanner()
    {
        $userId = $this->getAuthUserId();
        $file = $this->request->getFile('banner') ?: $this->request->getFile('file') ?: $this->request->getFile('media');

        if (!$file || !$file->isValid()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'No valid media file uploaded.'
            ]);
        }

        $allowedMimes = [
            'image/png', 'image/jpeg', 'image/jpg', 'image/webp', 'image/gif',
            'video/mp4', 'video/webm', 'video/quicktime', 'video/ogg'
        ];
        $mime = $file->getMimeType();
        if (!in_array($mime, $allowedMimes, true)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid media type. Supported formats: JPG, PNG, WebP, GIF, MP4, WebM.'
            ]);
        }

        $targetDir = FCPATH . 'files/profile_banners/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $ext = strtolower($file->guessExtension() ?: $file->getExtension() ?: 'bin');
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }

        $newName = 'banner_' . bin2hex(random_bytes(6)) . '_' . time() . '.' . $ext;
        $file->move($targetDir, $newName);

        $mediaUrl = base_url('files/profile_banners/' . $newName);
        $isVideo = strpos($mime, 'video/') === 0;

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Banner media uploaded successfully! ✨',
            'url' => $mediaUrl,
            'is_video' => $isVideo,
            'file_name' => $newName
        ]);
    }

    /**
     * POST /grid/api/profile/provision
     * Programmatically provisions a unique grid_id for a given user_id.
     * Request JSON: { "user_id": 123, "name": "Optional Name" }
     */
    public function provisionGridId()
    {
        $payload = $this->request->getJSON(true) ?: $this->request->getPost();
        $userId = (int) ($payload['user_id'] ?? $this->getAuthUserId());
        if (!$userId) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Valid user_id is required.'
            ]);
        }

        $name = trim((string) ($payload['name'] ?? ''));
        $gridId = $this->profileModel->provision_grid_id($userId, $name);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Grid ID provisioned successfully.',
            'data' => [
                'user_id' => $userId,
                'grid_id' => $gridId
            ]
        ]);
    }

    /**
     * POST /grid/api/profile/wizard-setup
     * Consolidated 4-Step Onboarding Wizard submission (see Views/GRID/API_SETUP_WIZARD.md).
     * Atomically updates users.{first_name,last_name,job_title}, upserts grid_profiles,
     * and associates the submitted skills with the user.
     */
    public function wizardSetup()
    {
        $userId = $this->getAuthUserId();
        $payload = $this->request->getJSON(true);
        if (!is_array($payload)) {
            $payload = $this->request->getPost();
        }

        $fullName = trim((string) ($payload['full_name'] ?? ''));
        $headline = trim((string) ($payload['headline'] ?? ''));

        if ($fullName === '' || $headline === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Full name and headline are required.'
            ]);
        }

        $headline = substr($headline, 0, 255);

        $nameParts = preg_split('/\s+/', $fullName, 2);
        $firstName = substr($nameParts[0], 0, 100);
        $lastName = isset($nameParts[1]) ? substr($nameParts[1], 0, 100) : '';

        $role = in_array($payload['role'] ?? '', ['candidate', 'recruiter']) ? $payload['role'] : 'candidate';

        $profileData = [
            'headline' => $headline,
            'bio' => trim((string) ($payload['bio'] ?? '')),
            'role' => $role,
            'is_open_to_work' => !empty($payload['is_open_to_work']) ? 1 : 0,
            'is_hiring' => !empty($payload['is_hiring']) ? 1 : 0,
            'location' => substr(trim((string) ($payload['location'] ?? '')), 0, 128),
            'website' => substr(trim((string) ($payload['website'] ?? '')), 0, 255),
            'github_url' => substr(trim((string) ($payload['github_url'] ?? '')), 0, 255),
            'linkedin_url' => substr(trim((string) ($payload['linkedin_url'] ?? '')), 0, 255),
            'is_wizard_completed' => 1
        ];

        $skillNames = [];
        if (!empty($payload['skills']) && is_array($payload['skills'])) {
            foreach ($payload['skills'] as $skill) {
                $skill = trim((string) $skill);
                if ($skill !== '') {
                    $skillNames[] = $skill;
                }
            }
        }

        // Resolve grid_id: honor a user-supplied handle (sanitized), reuse an existing
        // one so repeat wizard submissions don't churn it, or generate a fresh one.
        $payloadGridId = strtolower(trim((string) ($payload['grid_id'] ?? '')));
        $payloadGridId = preg_replace('/[^a-z0-9-]/', '', $payloadGridId);
        $payloadGridId = trim($payloadGridId, '-');

        if ($payloadGridId !== '') {
            $ownerProfile = $this->profileModel->get_profile_by_grid_id($payloadGridId);
            $gridId = ($ownerProfile && (int) $ownerProfile['user_id'] !== $userId)
                ? $this->profileModel->generate_unique_grid_id($fullName)
                : $payloadGridId;
        } else {
            $existingProfile = $this->profileModel->get_profile_by_user_id($userId);
            $gridId = !empty($existingProfile['grid_id'])
                ? $existingProfile['grid_id']
                : $this->profileModel->generate_unique_grid_id($fullName);
        }

        $profileData['grid_id'] = $gridId;

        $db = db_connect('default');
        $db->transStart();

        $this->Users_model->update_where(
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'job_title' => $headline
            ],
            ['id' => $userId]
        );

        $this->profileModel->save_profile($userId, $profileData);

        $skillsCount = $this->skillsModel->associate_user_skills($userId, $skillNames);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to save onboarding profile. Please try again.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Profile onboarding completed successfully! 🚀',
            'data' => [
                'user_id' => $userId,
                'grid_id' => $gridId,
                'name' => trim($firstName . ' ' . $lastName),
                'role' => $role,
                'skills_count' => $skillsCount
            ]
        ]);
    }

    // =========================================================================
    // 3. NETWORK & CONNECTIONS GRAPH API
    // =========================================================================

    /**
     * GET /grid/api/connections
     */
    public function getConnections()
    {
        $userId = $this->getAuthUserId();
        $rows = $this->connectionsModel->get_accepted_connections($userId);

        $connections = array_map(function($r) {
            return [
                'id' => (int) $r['id'],
                'name' => trim($r['first_name'] . ' ' . ($r['last_name'] ?? '')),
                'avatar' => $r['image'] ?? '',
                'headline' => $r['headline'] ?? 'Professional Member on GRID',
                'isOpenToWork' => (bool) ($r['is_open_to_work'] ?? 0),
                'connectedAt' => $r['connected_at']
            ];
        }, $rows);

        return $this->response->setJSON([
            'success' => true,
            'count' => count($connections),
            'data' => $connections
        ]);
    }

    /**
     * POST /grid/api/connections/request
     */
    public function requestConnection()
    {
        $currentUserId = $this->getAuthUserId();
        $targetUserId = (int) $this->request->getPost('target_user_id');

        if (!$targetUserId || $targetUserId === $currentUserId) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid target user.'
            ]);
        }

        $this->connectionsModel->send_request($currentUserId, $targetUserId);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Connection invitation sent.'
        ]);
    }

    /**
     * POST /grid/api/connections/respond
     */
    public function respondConnection()
    {
        $currentUserId = $this->getAuthUserId();
        $targetUserId = (int) $this->request->getPost('target_user_id');
        $action = trim((string) $this->request->getPost('action')); // 'accept' | 'decline'

        if (!in_array($action, ['accept', 'decline']) || !$targetUserId) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid parameters.'
            ]);
        }

        $this->connectionsModel->respond_request($currentUserId, $targetUserId, $action);

        return $this->response->setJSON([
            'success' => true,
            'message' => ($action === 'accept') ? 'Connection accepted!' : 'Connection declined.'
        ]);
    }

    // =========================================================================
    // 4. JOBS HUB & APPLICATION TRACKER API
    // =========================================================================

    /**
     * GET /grid/api/jobs
     */
    public function getJobs()
    {
        $userId = $this->getAuthUserId();
        $discipline = $this->request->getGet('discipline');
        $workplace = $this->request->getGet('workplace');

        $jobs = $this->jobsModel->get_active_jobs($userId, $discipline, $workplace, 50);

        return $this->response->setJSON([
            'success' => true,
            'count' => count($jobs),
            'data' => $jobs
        ]);
    }

    /**
     * POST /grid/api/jobs/apply
     */
    public function applyJob()
    {
        $userId = $this->getAuthUserId();
        $jobId = (int) $this->request->getPost('job_id');
        $coverNote = trim((string) $this->request->getPost('cover_note'));

        if (!$jobId) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Job ID required.'
            ]);
        }

        $applied = $this->jobsModel->apply_for_job($jobId, $userId, $coverNote);

        if (!$applied) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'You have already applied for this position.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Application submitted successfully! 🚀'
        ]);
    }

    /**
     * GET /grid/api/jobs/tracker
     */
    public function getApplicationTracker()
    {
        $userId = $this->getAuthUserId();
        $applications = $this->jobsModel->get_candidate_tracker($userId);

        return $this->response->setJSON([
            'success' => true,
            'count' => count($applications),
            'data' => $applications
        ]);
    }

    // =========================================================================
    // 5. RECRUITER MODE & JOB POSTING PIPELINE (ATS)
    // =========================================================================

    /**
     * POST /grid/api/jobs/create
     */
    public function createJob()
    {
        $userId = $this->getAuthUserId();
        $payload = $this->request->getJSON(true);
        if (!is_array($payload)) {
            $payload = $this->request->getPost();
        }

        $jobId = $this->jobsModel->create_job($userId, $payload);

        if (!$jobId) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Title, company name, discipline, workplace type, and location are required.'
            ]);
        }

        $createdJob = null;
        foreach ($this->jobsModel->get_recruiter_jobs($userId) as $job) {
            if ($job['id'] === $jobId) {
                $createdJob = $job;
                break;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Job listing published successfully! 🚀',
            'data' => $createdJob
        ]);
    }

    /**
     * GET /grid/api/recruiter/jobs
     */
    public function getRecruiterJobs()
    {
        $userId = $this->getAuthUserId();

        return $this->response->setJSON([
            'success' => true,
            'data' => $this->jobsModel->get_recruiter_jobs($userId)
        ]);
    }

    /**
     * GET /grid/api/recruiter/jobs/{jobId}/applicants
     */
    public function getJobApplicants($jobId = null)
    {
        $userId = $this->getAuthUserId();
        $applicants = $this->jobsModel->get_job_applicants((int) $jobId, $userId);

        if ($applicants === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Job not found or you do not have permission to view its applicants.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $applicants
        ]);
    }

    /**
     * POST /grid/api/recruiter/applications/status
     * Body: {"application_id": 123, "status": "interview"}
     */
    public function updateApplicationStatus()
    {
        $userId = $this->getAuthUserId();
        $payload = $this->request->getJSON(true);
        if (!is_array($payload)) {
            $payload = $this->request->getPost();
        }

        $applicationId = (int) ($payload['application_id'] ?? 0);
        $newStatus = trim((string) ($payload['status'] ?? ''));

        if (!$applicationId || $newStatus === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'application_id and status are required.'
            ]);
        }

        $result = $this->jobsModel->update_application_status($applicationId, $userId, $newStatus);

        switch ($result) {
            case 'ok':
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Candidate status updated successfully.'
                ]);
            case 'invalid_status':
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Invalid status value.'
                ]);
            case 'not_found':
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Application not found.'
                ]);
            case 'forbidden':
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'You do not have permission to update this application.'
                ]);
            default:
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to update application status.'
                ]);
        }
    }

    // =========================================================================
    // 6. CIRCLE CONNECT & DIRECT MESSAGING (UNIVERSAL HANDLE / grid_id)
    // =========================================================================

    /**
     * GET /grid/api/messages/conversations
     * List current user's conversations with opponent identity and unread count.
     */
    public function getConversations()
    {
        try {
            $authUserId = $this->getAuthUserId();
            $conversations = $this->messagesModel->get_user_conversations($authUserId);

            foreach ($conversations as &$c) {
                if (isset($c['opponent']['avatar'])) {
                    $c['opponent']['avatar'] = $this->resolveAvatarUrl($c['opponent']['avatar']);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $conversations
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error in getConversations: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to retrieve conversations: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * GET /grid/api/messages/thread/{conversationId}
     * Retrieve full thread messages and mark unread messages as read.
     */
    public function getThread($conversationId = null)
    {
        try {
            $authUserId = $this->getAuthUserId();
            $thread = $this->messagesModel->get_conversation_messages((int) $conversationId, $authUserId);

            if (!$thread) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Conversation not found or participant access denied.'
                ]);
            }

            if (isset($thread['opponent']['avatar'])) {
                $thread['opponent']['avatar'] = $this->resolveAvatarUrl($thread['opponent']['avatar']);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $thread
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error in getThread: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to retrieve thread messages: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * POST /grid/api/messages/send
     * Send a message to recipient, save to MySQL, and broadcast via WebSocket.
     */
    public function sendMessage()
    {
        try {
            $authUserId = $this->getAuthUserId();
            $payload = $this->request->getJSON(true);
            if (!is_array($payload)) {
                $payload = $this->request->getPost();
            }
            if (!is_array($payload)) {
                $payload = json_decode($this->request->getBody() ?: '{}', true) ?: [];
            }
            $recipientId = (int) ($payload['recipient_id'] ?? $this->request->getPost('recipient_id') ?? 0);
            $recipientGridId = trim((string) ($payload['recipient_grid_id'] ?? $this->request->getPost('recipient_grid_id') ?? ''));
            $message = trim((string) ($payload['message'] ?? $this->request->getPost('message') ?? ''));

            if ($recipientId <= 0 && !empty($recipientGridId)) {
                $profile = $this->profileModel->get_profile_by_grid_id($recipientGridId);
                if ($profile && !empty($profile['user_id'])) {
                    $recipientId = (int) $profile['user_id'];
                }
            }

            if ($recipientId <= 0 || empty($message)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Valid recipient ID and message body are required.'
                ]);
            }

            if ($recipientId === $authUserId) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Self-messaging is not permitted.'
                ]);
            }

            $sent = $this->messagesModel->send_message($authUserId, $recipientId, $message);

            // Fetch sender profile info for rich WebSocket notification
            $senderUser = $this->Users_model->get_one($authUserId);
            $senderName = $senderUser ? trim(($senderUser->first_name ?? '') . ' ' . ($senderUser->last_name ?? '')) : 'Member';
            $senderProfile = $this->profileModel->get_profile_by_user_id($authUserId);
            $senderAvatar = $this->resolveAvatarUrl($senderProfile['avatar'] ?? '');

            // Broadcast via WebSocket Gateway in background
            $this->broadcastRealtimeMessage([
                'type' => 'NEW_MESSAGE',
                'data' => [
                    'id' => $sent['id'],
                    'conversation_id' => $sent['conversation_id'],
                    'sender_id' => $authUserId,
                    'recipient_id' => $recipientId,
                    'sender_name' => $senderName,
                    'sender_avatar' => $senderAvatar,
                    'message' => $message,
                    'created_at' => $sent['created_at']
                ]
            ]);

            return $this->response->setJSON([
                'success' => true,
                'data' => $sent
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error in sendMessage: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to send message: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * GET /grid/api/messages/start/{targetIdentifier}
     * Find or create conversation with user ID or public grid_id.
     */
    public function startConversation($targetIdentifier = null)
    {
        try {
            $authUserId = $this->getAuthUserId();
            $targetUserId = 0;

            if (is_numeric($targetIdentifier)) {
                $targetUserId = (int) $targetIdentifier;
            } else {
                $profile = $this->profileModel->get_profile_by_grid_id($targetIdentifier);
                if ($profile && !empty($profile['user_id'])) {
                    $targetUserId = (int) $profile['user_id'];
                }
            }

            if ($targetUserId <= 0) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Target profile not found.'
                ]);
            }

            $conversationId = $this->messagesModel->get_or_create_conversation($authUserId, $targetUserId);
            $thread = $this->messagesModel->get_conversation_messages($conversationId, $authUserId);

            if (isset($thread['opponent']['avatar'])) {
                $thread['opponent']['avatar'] = $this->resolveAvatarUrl($thread['opponent']['avatar']);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $thread
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error in startConversation: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to initiate conversation: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Dispatch HTTP POST to local WebSocket Gateway daemon (port 8085)
     */
    protected function broadcastRealtimeMessage(array $payload)
    {
        if (!function_exists('curl_init')) {
            return;
        }
        $ch = curl_init('http://127.0.0.1:8085/broadcast');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 200);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 150);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        @curl_exec($ch);
        @curl_close($ch);
    }

    // =========================================================================
    // 7. HOME FEED POSTS
    // =========================================================================

    /**
     * POST /grid/api/posts/create
     * Body: {"content": "...", "tags": ["#Tag1"], "media_url": null, "type": "general"}
     */
    public function createPost()
    {
        $userId = $this->getAuthUserId();
        $payload = $this->request->getJSON(true);
        if (!is_array($payload)) {
            $payload = $this->request->getPost();
        }

        $post = $this->postsModel->create_post($userId, $payload);

        if (!$post) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Post content is required.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Post published successfully.',
            'data' => $post
        ]);
    }

    /**
     * GET /grid/api/posts?limit=20&offset=0
     */
    public function getPosts()
    {
        $userId = $this->getAuthUserId();
        $limit = (int) ($this->request->getGet('limit') ?: 20);
        $offset = (int) ($this->request->getGet('offset') ?: 0);

        $limit = max(1, min($limit, 100));
        $offset = max(0, $offset);

        return $this->response->setJSON([
            'success' => true,
            'data' => $this->postsModel->get_feed_posts($limit, $offset, $userId)
        ]);
    }

    /**
     * GET /grid/api/posts/user/{userId?}
     * A user's own posts merged with what they've reposted, newest first.
     * Defaults to the authenticated user when no userId is given.
     */
    public function getUserPosts($userId = null)
    {
        $currentUserId = $this->getAuthUserId();
        $targetUserId = $userId ? (int) $userId : $currentUserId;

        $limit = (int) ($this->request->getGet('limit') ?: 20);
        $offset = (int) ($this->request->getGet('offset') ?: 0);

        $limit = max(1, min($limit, 100));
        $offset = max(0, $offset);

        return $this->response->setJSON([
            'success' => true,
            'data' => $this->postsModel->get_user_posts($targetUserId, $limit, $offset, $currentUserId)
        ]);
    }

    /**
     * POST /grid/api/posts/like or POST /grid/api/posts/{id}/like
     * Body: {"post_id": 6} or {"id": "db-6"}
     */
    public function toggleLike($id = null)
    {
        $userId = $this->getAuthUserId();

        if (!$id) {
            $payload = $this->request->getJSON(true);
            if (!is_array($payload)) {
                $payload = $this->request->getPost();
            }
            $id = $payload['post_id'] ?? $payload['id'] ?? null;
        }

        $numericId = (int) preg_replace('/[^0-9]/', '', (string) $id);
        if ($numericId <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Valid post ID is required.'
            ]);
        }

        $result = $this->postsModel->toggle_like($numericId, $userId);

        return $this->response->setJSON([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * POST /grid/api/posts/repost or POST /grid/api/posts/{id}/repost
     * Body: {"post_id": 6} or {"id": "db-6"}
     * Shares the original post into the caller's own feed/profile; calling
     * again on an already-reposted post un-reposts it.
     */
    public function toggleRepost($id = null)
    {
        $userId = $this->getAuthUserId();

        if (!$id) {
            $payload = $this->request->getJSON(true);
            if (!is_array($payload)) {
                $payload = $this->request->getPost();
            }
            $id = $payload['post_id'] ?? $payload['id'] ?? null;
        }

        $numericId = (int) preg_replace('/[^0-9]/', '', (string) $id);
        if ($numericId <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Valid post ID is required.'
            ]);
        }

        $result = $this->postsModel->toggle_repost($numericId, $userId);

        if (($result['error'] ?? null) === 'self_repost') {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => "You can't repost your own post.",
                'data' => $result
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * GET /grid/api/posts/{postId}/comments
     */
    public function getComments($postId = null)
    {
        $numericId = (int) preg_replace('/[^0-9]/', '', (string) $postId);
        if ($numericId <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Valid post ID is required.'
            ]);
        }

        $limit = (int) ($this->request->getGet('limit') ?: 50);
        $offset = (int) ($this->request->getGet('offset') ?: 0);

        $comments = $this->postsModel->get_comments($numericId, $limit, $offset);

        return $this->response->setJSON([
            'success' => true,
            'data' => $comments
        ]);
    }

    /**
     * POST /grid/api/posts/comment or POST /grid/api/posts/{postId}/comments
     * Body: {"post_id": 6, "comment": "Great work!"}
     */
    public function addComment($postId = null)
    {
        $userId = $this->getAuthUserId();
        $payload = $this->request->getJSON(true);
        if (!is_array($payload)) {
            $payload = $this->request->getPost();
        }

        if (!$postId) {
            $postId = $payload['post_id'] ?? $payload['id'] ?? null;
        }

        $numericId = (int) preg_replace('/[^0-9]/', '', (string) $postId);
        if ($numericId <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Valid post ID is required.'
            ]);
        }

        $text = trim((string) ($payload['comment'] ?? $payload['comment_text'] ?? $payload['text'] ?? ''));
        if ($text === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Comment text is required.'
            ]);
        }

        $comment = $this->postsModel->add_comment($numericId, $userId, $text);
        if (!$comment) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to save comment.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Comment added successfully.',
            'data' => $comment
        ]);
    }

    /**
     * DELETE /grid/api/posts/comment/{id} or POST /grid/api/posts/comment/delete
     */
    public function deleteComment($commentId = null)
    {
        $userId = $this->getAuthUserId();
        $isAdmin = !empty($this->login_user->is_admin);

        if (!$commentId) {
            $payload = $this->request->getJSON(true);
            if (!is_array($payload)) {
                $payload = $this->request->getPost();
            }
            $commentId = $payload['comment_id'] ?? $payload['id'] ?? null;
        }

        $numericId = (int) preg_replace('/[^0-9]/', '', (string) $commentId);
        if ($numericId <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Valid comment ID is required.'
            ]);
        }

        $deleted = $this->postsModel->delete_comment($numericId, $userId, $isAdmin);
        if (!$deleted) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'You do not have permission to delete this comment.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Comment deleted successfully.'
        ]);
    }

    /**
     * POST /grid/api/posts/delete
     * Body: {"id": 6} or {"id": "db-6"}
     */
    public function deletePost($id = null)
    {
        $userId = $this->getAuthUserId();
        $isAdmin = !empty($this->login_user->is_admin);

        if (!$id) {
            $payload = $this->request->getJSON(true);
            if (!is_array($payload)) {
                $payload = $this->request->getPost();
            }
            $id = $payload['id'] ?? $payload['post_id'] ?? null;
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Post ID is required.'
            ]);
        }

        $numericId = (int) preg_replace('/[^0-9]/', '', (string) $id);
        if ($numericId <= 0) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Post removed.'
            ]);
        }

        $deleted = $this->postsModel->delete_post($numericId, $userId, $isAdmin);
        if (!$deleted) {
            // Check if post exists at all
            $postRow = $this->postsModel->db->table('grid_posts')->where('id', $numericId)->get()->getRowArray();
            if (!$postRow) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Post not found.'
                ]);
            }
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'You do not have permission to delete this post.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Post deleted successfully.'
        ]);
    }

    /**
     * POST /grid/api/upload
     * Universal file upload endpoint for posts, messages, teams, and profiles.
     * Accepts multipart/form-data:
     * - 'file': Uploaded file object
     * - 'type': Optional category ('post', 'message', 'profile', 'team')
     */
    public function upload()
    {
        $file = $this->request->getFile('file');
        if (!$file && $this->request->getFile('image')) {
            $file = $this->request->getFile('image');
        }
        if (!$file && $this->request->getFile('video')) {
            $file = $this->request->getFile('video');
        }

        if (!$file || !$file->isValid()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => $file ? $file->getErrorString() : 'No file uploaded.'
            ]);
        }

        // Validate MIME type (images and videos supported)
        $imageMimes = [
            'image/png',
            'image/jpeg',
            'image/jpg',
            'image/webp',
            'image/gif'
        ];
        $videoMimes = [
            'video/mp4',
            'video/webm',
            'video/quicktime',
            'video/x-matroska'
        ];

        $mime = strtolower((string) $file->getMimeType());
        $isVideo = in_array($mime, $videoMimes, true);
        $isImage = in_array($mime, $imageMimes, true);

        if (!$isImage && !$isVideo) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid file format. Supported: JPG, PNG, WEBP, GIF, MP4, and WEBM.'
            ]);
        }

        // Size limits: 15MB for images, 100MB for videos
        $maxSizeBytes = $isVideo ? (100 * 1024 * 1024) : (15 * 1024 * 1024);
        if ($file->getSize() > $maxSizeBytes) {
            $limitMb = $isVideo ? '100MB' : '15MB';
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => "File size exceeds maximum {$limitMb} limit."
            ]);
        }

        $type = strtolower((string) ($this->request->getPost('type') ?: 'post'));
        $sanitizedType = preg_replace('/[^a-z0-9_-]/i', '', $type);

        // Determine dedicated folder destination
        if (strpos($type, 'snippet') !== false) {
            $subDir = 'files/grid/snippets/';
        } elseif (strpos($type, 'post') !== false || $type === 'feed' || $type === 'general') {
            $subDir = 'files/grid/posts/';
        } elseif ($isVideo || strpos($type, 'video') !== false) {
            $subDir = 'files/grid/profile/videos/';
        } elseif (in_array($type, ['profile', 'avatar', 'banner', 'profile_image'], true) || strpos($type, 'profile') !== false) {
            $subDir = 'files/grid/profile/images/';
        } else {
            $subDir = 'files/grid/posts/';
        }

        $targetDir = FCPATH . $subDir;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $ext = strtolower($file->guessExtension() ?: $file->getExtension() ?: ($isVideo ? 'mp4' : 'png'));
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }

        $newName = 'grid_' . $sanitizedType . '_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
        $file->move($targetDir, $newName);

        $relativeUrl = '/' . $subDir . $newName;

        return $this->response->setJSON([
            'success' => true,
            'message' => 'File uploaded successfully.',
            'file_url' => $relativeUrl,
            'file_name' => $newName,
            'media_type' => $isVideo ? 'video' : 'image',
            'file_size' => $file->getSize()
        ]);
    }

    /**
     * GET /grid/api/challenges/top
     * Returns top active company-sponsored challenges for Home Suggested Topics and Events
     */
    public function getTopChallenges()
    {
        try {
            $limit = (int) ($this->request->getGet('limit') ?: 6);
            $challenges = $this->challengesModel->get_top_active_challenges($limit);

            return $this->response->setJSON([
                'success' => true,
                'data' => $challenges,
                'count' => count($challenges)
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error fetching top challenges: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Could not retrieve active challenges.'
            ]);
        }
    }

    /**
     * POST /grid/api/challenges/create
     * Creates a new company-sponsored challenge
     */
    public function createChallenge()
    {
        try {
            $userId = $this->getAuthUserId();
            $data = $this->request->getJSON(true) ?: $this->request->getPost();

            $title = trim($data['title'] ?? '');
            if (empty($title)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Challenge title is required.'
                ]);
            }

            $tag = trim($data['tag'] ?? '');
            if (!empty($tag) && !str_starts_with($tag, '#')) {
                $tag = '#' . $tag;
            }
            if (empty($tag)) {
                $tag = '#' . preg_replace('/[^a-zA-Z0-9]/', '', $title);
            }

            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($title)));
            $slug = trim($slug, '-') . '-' . substr(bin2hex(random_bytes(4)), 0, 4);

            $prizeAmount = trim($data['prize_amount'] ?? '$5,000');
            $description = trim($data['description'] ?? '');
            $companyId = (int) ($data['company_id'] ?? 1);
            $durationDays = (int) ($data['duration_days'] ?? 21);

            $insertData = [
                'company_id' => $companyId,
                'creator_user_id' => $userId,
                'title' => $title,
                'slug' => $slug,
                'tag' => $tag,
                'description' => $description,
                'prize_amount' => $prizeAmount,
                'prize_details' => $data['prize_details'] ?? null,
                'icon_type' => $data['icon_type'] ?? 'trophy',
                'bg_image' => $data['bg_image'] ?? 'https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?w=600&auto=format&fit=crop&q=80',
                'start_date' => date('Y-m-d H:i:s'),
                'end_date' => date('Y-m-d H:i:s', strtotime("+{$durationDays} days")),
                'status' => 'active',
                'submissions_count' => 0
            ];

            $newId = $this->challengesModel->create_challenge($insertData);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Challenge published successfully! 🚀',
                'challenge_id' => $newId,
                'tag' => $tag,
                'slug' => $slug
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error creating challenge: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to create challenge: ' . $e->getMessage()
            ]);
        }
    }
}