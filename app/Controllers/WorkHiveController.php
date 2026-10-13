<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\WorkHiveModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class WorkHiveController extends \CodeIgniter\Controller
{
    protected WorkHiveModel $workHiveModel;
    protected WorkHiveModel $attendanceModel;
    protected WorkHiveModel $userModel;
    protected WorkHiveModel $workModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        date_default_timezone_set('Asia/Kolkata');
        $this->workHiveModel = new WorkHiveModel();
        $this->attendanceModel = $this->workHiveModel;
        $this->userModel = $this->workHiveModel;
        $this->workModel = $this->workHiveModel;
    }

    public function ping()
    {
        return $this->j(array(
            'success' => true,
            'pong' => true,
            'file' => __FILE__,
            'time_ist' => date('Y-m-d H:i:s'),
            'extends' => 'CodeIgniter\\Controller',
        ));
    }

    public function index()
    {
        helper(array('url'));
        $sess = session();
        $role = $this->role((string) ($sess->get('attend_role') ?? ''));

        return view('items/work_hive', array(
            'isLoggedIn' => (bool) $sess->get('attend_uid'),
            'userName' => (string) ($sess->get('attend_name') ?? ''),
            'empId' => (string) ($sess->get('attend_employee_id') ?? ''),
            'userRole' => $role,
            'canEdit' => (bool) $sess->get('attend_can_edit'),
            'canAnalytics' => (bool) $sess->get('attend_can_analytics'),
            'canAddEmp' => (bool) $sess->get('attend_can_add_emp'),
            'department' => (string) ($sess->get('attend_department') ?? ''),
            'apiBase' => site_url('work-hive/api'),
            'logoutUrl' => site_url('work-hive/logout'),
            'pageUrl' => site_url('work-hive'),
            'workUrl' => site_url('work-hive'),
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ));
    }

    public function api(string $action = '')
    {
        date_default_timezone_set('Asia/Kolkata');

        try {
            switch ($action) {
                case 'ping':
                    return $this->j(array('success' => true, 'pong' => true, 'file' => __FILE__, 'time_ist' => date('Y-m-d H:i:s')));

                case 'login':
                    return $this->j($this->actLogin());

                case 'check_in':
                    return $this->j($this->auth(function () { return $this->actCheckIn(); }));

                case 'check_out':
                    return $this->j($this->auth(function () { return $this->attendanceModel->checkOut($this->uid()); }));

                case 'today_status':
                    return $this->j($this->auth(function () { return $this->actTodayStatus(); }));

                case 'my_attendance':
                    return $this->j($this->auth(function () { return $this->actMyAttendance(); }));

                case 'edit_attendance':
                    return $this->j($this->auth(function () { return $this->authEdit(function () { return $this->actEditAttendance(); }); }));

                case 'dashboard_data':
                    return $this->j($this->auth(function () { return $this->authAnalytics(function () { return $this->actDashboard(); }); }));

                case 'all_attendance':
                    return $this->j($this->auth(function () { return $this->authEdit(function () { return $this->actAllAttendance(); }); }));

                case 'all_users':
                    return $this->j($this->auth(function () {
                        return $this->authEdit(function () {
                            return array('success' => true, 'users' => $this->userModel->getAllActive());
                        });
                    }));

                case 'add_employee':
                    return $this->j($this->auth(function () { return $this->authAddEmp(function () { return $this->actAddEmployee(); }); }));

                case 'roles':
                    return $this->j(array('success' => true, 'roles' => $this->userModel->getRoles()));

                case 'departments':
                    return $this->j($this->auth(function () {
                        return array('success' => true, 'departments' => $this->userModel->getDepartments());
                    }));

                case 'work_projects':
                    return $this->j($this->auth(function () { return $this->actWorkProjects(); }));

                case 'work_project_detail':
                    return $this->j($this->auth(function () { return $this->actWorkProjectDetail(); }));

                case 'work_save_project':
                    return $this->j($this->auth(function () { return $this->actWorkSaveProject(); }));

                case 'work_delete_project':
                    return $this->j($this->auth(function () { return $this->actWorkDeleteProject(); }));

                case 'work_members':
                    return $this->j($this->auth(function () { return $this->actWorkMembers(); }));

                case 'work_tasks':
                    return $this->j($this->auth(function () { return $this->actWorkTasks(); }));

                case 'work_save_task':
                    return $this->j($this->auth(function () { return $this->actWorkSaveTask(); }));

                case 'work_delete_task':
                    return $this->j($this->auth(function () { return $this->actWorkDeleteTask(); }));

                case 'work_save_update':
                    return $this->j($this->auth(function () { return $this->actWorkSaveUpdate(); }));

                case 'work_updates':
                    return $this->j($this->auth(function () { return $this->actWorkUpdates(); }));

                case 'work_review_update':
                    return $this->j($this->auth(function () { return $this->authEdit(function () { return $this->actWorkReviewUpdate(); }); }));

                case 'work_daily':
                    return $this->j($this->auth(function () { return $this->actWorkDaily(); }));

                case 'work_week':
                    return $this->j($this->auth(function () { return $this->actWorkWeek(); }));

                case 'work_month':
                    return $this->j($this->auth(function () { return $this->actWorkMonth(); }));

                case 'work_compare':
                    return $this->j($this->auth(function () { return $this->actWorkCompare(); }));

                case 'work_dashboard':
                    return $this->j($this->auth(function () { return $this->actWorkDashboard(); }));

                case 'work_command_center':
                    return $this->j($this->auth(function () { return $this->actWorkCommandCenter(); }));

                case 'work_employee_detail':
                    return $this->j($this->auth(function () { return $this->actWorkEmployeeDetail(); }));

                default:
                    return $this->j(array('success' => false, 'message' => 'Unknown action: ' . htmlspecialchars($action)), 404);
            }
        } catch (Throwable $e) {
            return $this->j(array(
                'success' => false,
                'message' => 'Server error.',
                'debug' => ENVIRONMENT !== 'production' ? $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine() : null,
            ), 500);
        }
    }

    public function logout()
    {
        helper(array('url'));
        $sess = session();
        foreach (array_keys((array) $sess->get()) as $k) {
            if (strpos((string) $k, 'attend_') === 0) {
                $sess->remove($k);
            }
        }
        return redirect()->to(site_url('work-hive'));
    }

    private function actLogin(): array
    {
        $p = $this->request->getPost();
        $eid = strtoupper(trim((string) ($p['employee_id'] ?? '')));
        $pwd = (string) ($p['password'] ?? '');
        $rf = $this->role((string) ($p['role'] ?? ''));

        if ($eid === '' || $pwd === '') {
            return array('success' => false, 'message' => 'Employee ID and password are required.');
        }

        $user = $this->userModel->findByEmployeeId($eid);
        if (!$user) {
            return array('success' => false, 'message' => 'Invalid Employee ID or password.');
        }

        $storedPassword = (string) ($user['password_hash'] ?? ($user['password'] ?? ''));
        if (!$this->userModel->verifyPassword($pwd, $storedPassword)) {
            return array('success' => false, 'message' => 'Invalid Employee ID or password.');
        }

        $dbRole = $this->role((string) ($user['role_name'] ?? ''));

        if ($rf !== '' && $dbRole !== $rf) {
            return array('success' => false, 'message' => 'Role does not match. Selected: ' . $rf . ', DB: ' . $dbRole);
        }

        $canEdit = !empty($user['can_edit']) || $dbRole === 'admin';
        $canAddEmp = in_array($dbRole, array('hr', 'manager', 'admin'), true);

        $sess = session();
        $sess->regenerate(true);
        $sess->set(array(
            'attend_uid' => (int) $user['id'],
            'attend_employee_id' => (string) $user['employee_id'],
            'attend_name' => (string) $user['full_name'],
            'attend_role' => $dbRole,
            'attend_can_edit' => $canEdit,
            'attend_can_analytics' => !empty($user['can_view_analytics']),
            'attend_can_add_emp' => $canAddEmp,
            'attend_department' => (string) ($user['department_name'] ?? ''),
            'attend_department_id' => (int) ($user['department_id'] ?? 0),
        ));

        $this->workModel->ensureWorkMemberForAttendUser((int) $user['id']);

        return array('success' => true, 'message' => 'Login successful.');
    }

    private function actCheckIn(): array
    {
        $p = $this->request->getPost();
        return $this->attendanceModel->checkIn($this->uid(), array(
            'latitude' => ($p['latitude'] ?? '') !== '' ? (float) $p['latitude'] : null,
            'longitude' => ($p['longitude'] ?? '') !== '' ? (float) $p['longitude'] : null,
            'name' => substr(trim((string) ($p['location_name'] ?? '')), 0, 200),
            'ip' => (string) $this->request->getIPAddress(),
            'device' => $this->ua(),
        ));
    }

    private function actTodayStatus(): array
    {
        $sess = session();
        $ctx = $this->workContext();
        return array(
            'success' => true,
            'record' => $this->attendanceModel->getTodayRecord($this->uid()),
            'work_today' => $this->workModel->getAttendanceWorkDaily(array('from' => date('Y-m-d'), 'to' => date('Y-m-d')), $ctx),
            'user' => array(
                'name' => (string) ($sess->get('attend_name') ?? ''),
                'employee_id' => (string) ($sess->get('attend_employee_id') ?? ''),
                'role' => (string) ($sess->get('attend_role') ?? ''),
                'can_edit' => (bool) $sess->get('attend_can_edit'),
                'can_analytics' => (bool) $sess->get('attend_can_analytics'),
                'can_add_emp' => (bool) $sess->get('attend_can_add_emp'),
            ),
        );
    }

    private function actMyAttendance(): array
    {
        $from = $this->date($this->request->getGet('from') ?? date('Y-m-01'), date('Y-m-01'));
        $to = $this->date($this->request->getGet('to') ?? date('Y-m-d'), date('Y-m-d'));
        if ($from > $to) {
            return array('success' => false, 'message' => 'Invalid date range.');
        }
        return array('success' => true, 'records' => $this->attendanceModel->getUserAttendance($this->uid(), $from, $to));
    }

    private function actEditAttendance(): array
    {
        $p = $this->request->getPost();
        $tid = (int) ($p['user_id'] ?? 0);
        $date = $this->date((string) ($p['date'] ?? ''), '');
        $status = $this->status((string) ($p['status'] ?? ''));

        if ($tid <= 0 || $date === '') {
            return array('success' => false, 'message' => 'Missing user_id or date.');
        }
        if ($status === '') {
            return array('success' => false, 'message' => 'Invalid status.');
        }

        $ci = $this->dt($date, (string) ($p['check_in'] ?? ''));
        $co = $this->dt($date, (string) ($p['check_out'] ?? ''));

        if ($ci && $co && strtotime($co) <= strtotime($ci)) {
            return array('success' => false, 'message' => 'Check-out must be after check-in.');
        }

        return $this->attendanceModel->editAttendance($tid, $date, array(
            'status' => $status,
            'check_in' => $ci,
            'check_out' => $co,
            'remarks' => substr(trim((string) ($p['remarks'] ?? '')), 0, 500),
        ), $this->uid());
    }

    private function actAddEmployee(): array
    {
        $p = $this->request->getPost();
        $result = $this->userModel->addEmployee(array(
            'employee_id' => (string) ($p['employee_id'] ?? ''),
            'full_name' => (string) ($p['full_name'] ?? ''),
            'email' => (string) ($p['email'] ?? ''),
            'password' => (string) ($p['password'] ?? ''),
            'role_id' => (int) ($p['role_id'] ?? 0),
            'department_id' => (int) ($p['department_id'] ?? 0),
            'designation' => (string) ($p['designation'] ?? ''),
        ));

        if (!empty($result['success'])) {
            $new = $this->userModel->findByEmployeeId((string) ($result['employee_id'] ?? ''));
            if ($new && !empty($new['id'])) {
                $this->workModel->ensureWorkMemberForAttendUser((int) $new['id']);
            }
        }

        return $result;
    }

    private function actDashboard(): array
    {
        $today = date('Y-m-d');
        $snap = $this->attendanceModel->getTodaySnapshot();
        $t = array('present' => 0, 'absent' => 0, 'wfh' => 0, 'leave' => 0, 'half_day' => 0, 'holiday' => 0, 'not_marked' => 0, 'work_updates' => 0, 'work_hours' => 0);
        foreach ($snap as $r) {
            $s = $r['status'] ?? '';
            if ($s === 'present') {
                $t['present']++;
            } elseif ($s === 'absent') {
                $t['absent']++;
            } elseif ($s === 'work_from_home') {
                $t['wfh']++;
            } elseif ($s === 'leave') {
                $t['leave']++;
            } elseif ($s === 'half_day') {
                $t['half_day']++;
            } elseif ($s === 'holiday') {
                $t['holiday']++;
            } else {
                $t['not_marked']++;
            }
            $t['work_updates'] += (int) ($r['work_update_count'] ?? 0);
            $t['work_hours'] += (float) ($r['work_logged_hours'] ?? 0);
        }

        return array(
            'success' => true,
            'today' => $snap,
            'totals' => $t,
            'dept_stats' => $this->attendanceModel->getDeptSummary($today),
            'trend' => $this->attendanceModel->getAttendanceTrend(30),
            'monthly' => $this->attendanceModel->getMonthlySummary((int) date('Y'), (int) date('m')),
            'work_dashboard' => $this->workModel->getDashboard($this->workContext()),
        );
    }

    private function actAllAttendance(): array
    {
        $from = $this->date($this->request->getGet('from') ?? date('Y-m-01'), date('Y-m-01'));
        $to = $this->date($this->request->getGet('to') ?? date('Y-m-d'), date('Y-m-d'));
        $deptId = (int) ($this->request->getGet('dept') ?? 0);
        if ($from > $to) {
            return array('success' => false, 'message' => 'Invalid date range.');
        }
        return array('success' => true, 'records' => $this->attendanceModel->getAllAttendance($from, $to, $deptId));
    }

    private function actWorkProjects(): array
    {
        $filters = array(
            'search' => $this->request->getGet('search'),
            'status' => $this->request->getGet('status'),
            'priority' => $this->request->getGet('priority'),
            'project_type' => $this->request->getGet('project_type'),
            'work_type' => $this->request->getGet('work_type'),
            'member_id' => $this->request->getGet('member_id'),
            'today' => $this->request->getGet('today'),
        );
        $ctx = $this->workContext();
        return array(
            'success' => true,
            'projects' => $this->workModel->getProjects($filters, $ctx),
            'members' => $this->workModel->getMembers(true, $ctx),
            'types' => $this->workModel->getProjectTypes(),
            'can_manage' => $this->workModel->canManageWork($ctx),
            'role' => (string) ($ctx['role'] ?? 'employee'),
        );
    }

    private function actWorkProjectDetail(): array
    {
        $id = (int) ($this->request->getGet('id') ?? 0);
        if ($id <= 0) {
            return array('success' => false, 'message' => 'Invalid project/schedule selected.');
        }
        $project = $this->workModel->getProject($id, $this->workContext());
        if (!$project) {
            return array('success' => false, 'message' => 'Project/schedule not found or access denied.');
        }
        return array('success' => true, 'project' => $project);
    }

    private function actWorkMembers(): array
    {
        return array('success' => true, 'members' => $this->workModel->getMembers(true, $this->workContext()));
    }

    private function actWorkSaveProject(): array
    {
        $p = $this->request->getPost();
        $name = $this->clean((string) ($p['project_name'] ?? ''));
        if ($name === '') {
            return array('success' => false, 'message' => 'Project / schedule name is required.');
        }

        $memberIds = $this->request->getPost('member_ids');
        if (!is_array($memberIds)) {
            $memberIdsText = trim((string) ($memberIds ?? $this->request->getPost('member_ids_csv') ?? ''));
            $memberIds = $memberIdsText !== '' ? preg_split('/\s*,\s*/', $memberIdsText) : array();
        }
        $workType = $this->allowed((string) ($p['work_type'] ?? 'project'), array('project', 'schedule'), 'project');
        $ctx = $this->workContext();
        $projectId = $this->workModel->saveProject(array(
            'id' => (int) ($p['id'] ?? 0),
            'work_type' => $workType,
            'project_code' => $this->clean((string) ($p['project_code'] ?? '')),
            'project_name' => $name,
            'client_name' => $this->clean((string) ($p['client_name'] ?? '')),
            'project_type' => $this->clean((string) ($p['project_type'] ?? '')),
            'category' => $this->clean((string) ($p['category'] ?? '')),
            'department_id' => (int) ($p['department_id'] ?? 0),
            'visibility' => $this->allowed((string) ($p['visibility'] ?? 'assigned'), array('assigned', 'department', 'all'), 'assigned'),
            'priority' => $this->allowed((string) ($p['priority'] ?? 'medium'), array('low', 'medium', 'high', 'critical'), 'medium'),
            'status' => $this->allowed((string) ($p['status'] ?? 'planned'), array('planned', 'discovery', 'in_progress', 'review', 'blocked', 'on_hold', 'completed', 'closed', 'cancelled'), 'planned'),
            'progress' => (int) ($p['progress'] ?? 0),
            'start_date' => $this->date((string) ($p['start_date'] ?? ''), ''),
            'due_date' => $this->date((string) ($p['due_date'] ?? ''), ''),
            'schedule_frequency' => $this->allowed((string) ($p['schedule_frequency'] ?? 'one_time'), array('one_time', 'daily', 'weekly', 'monthly', 'custom'), 'one_time'),
            'schedule_start_date' => $this->date((string) ($p['schedule_start_date'] ?? ''), ''),
            'schedule_end_date' => $this->date((string) ($p['schedule_end_date'] ?? ''), ''),
            'repeat_days' => $this->clean((string) ($p['repeat_days'] ?? '')),
            'owner_name' => $this->clean((string) ($p['owner_name'] ?? '')),
            'current_focus' => $this->cleanLong((string) ($p['current_focus'] ?? '')),
            'next_step' => $this->cleanLong((string) ($p['next_step'] ?? '')),
            'blockers' => $this->cleanLong((string) ($p['blockers'] ?? '')),
            'description' => $this->cleanLong((string) ($p['description'] ?? '')),
        ), $memberIds, $ctx);

        if ($projectId <= 0) {
            return array('success' => false, 'message' => 'Project / schedule could not be saved. Check permission.');
        }
        return array('success' => true, 'message' => ucfirst($workType) . ' saved successfully.', 'project_id' => $projectId);
    }

    private function actWorkDeleteProject(): array
    {
        $id = (int) ($this->request->getPost('id') ?? 0);
        if ($id <= 0) {
            return array('success' => false, 'message' => 'Invalid project/schedule selected.');
        }
        if (!$this->workModel->archiveProject($id, $this->workContext())) {
            return array('success' => false, 'message' => 'Project/schedule could not be archived or access denied.');
        }
        return array('success' => true, 'message' => 'Project/schedule archived successfully.', 'id' => $id);
    }

    private function actWorkTasks(): array
    {
        $filters = array(
            'project_id' => $this->request->getGet('project_id'),
            'task_id' => $this->request->getGet('task_id'),
            'member_id' => $this->request->getGet('member_id'),
            'work_type' => $this->request->getGet('work_type'),
            'status' => $this->request->getGet('status'),
            'today' => $this->request->getGet('today'),
            'search' => $this->request->getGet('search'),
        );
        return array('success' => true, 'tasks' => $this->workModel->getTasks($filters, $this->workContext()));
    }

    private function actWorkSaveTask(): array
    {
        $p = $this->request->getPost();
        $title = $this->clean((string) ($p['task_title'] ?? ''));
        if ($title === '') {
            return array('success' => false, 'message' => 'Task title is required.');
        }
        $taskId = $this->workModel->saveTask(array(
            'id' => (int) ($p['id'] ?? 0),
            'project_id' => (int) ($p['project_id'] ?? 0),
            'parent_task_id' => (int) ($p['parent_task_id'] ?? 0),
            'task_code' => $this->clean((string) ($p['task_code'] ?? '')),
            'task_title' => $title,
            'task_description' => $this->cleanLong((string) ($p['task_description'] ?? '')),
            'task_type' => $this->allowed((string) ($p['task_type'] ?? 'project_task'), array('project_task', 'schedule_task'), 'project_task'),
            'priority' => $this->allowed((string) ($p['priority'] ?? 'medium'), array('low', 'medium', 'high', 'critical'), 'medium'),
            'status' => $this->allowed((string) ($p['status'] ?? 'todo'), array('todo', 'in_progress', 'review', 'blocked', 'paused', 'completed', 'cancelled'), 'todo'),
            'progress' => (int) ($p['progress'] ?? 0),
            'assigned_to_member_id' => (int) ($p['assigned_to_member_id'] ?? 0),
            'start_date' => $this->date((string) ($p['start_date'] ?? ''), ''),
            'due_date' => $this->date((string) ($p['due_date'] ?? ''), ''),
            'planned_start_time' => $this->clean((string) ($p['planned_start_time'] ?? '')),
            'planned_end_time' => $this->clean((string) ($p['planned_end_time'] ?? '')),
            'planned_hours' => (float) ($p['planned_hours'] ?? 0),
            'checklist' => (string) ($p['checklist'] ?? ($p['checklist_json'] ?? '')),
        ), $this->workContext());

        if ($taskId <= 0) {
            return array('success' => false, 'message' => 'Task could not be saved. Check project/schedule access.');
        }
        return array('success' => true, 'message' => 'Task saved successfully.', 'task_id' => $taskId);
    }

    private function actWorkDeleteTask(): array
    {
        $id = (int) ($this->request->getPost('id') ?? 0);
        if ($id <= 0) {
            return array('success' => false, 'message' => 'Invalid task selected.');
        }
        if (!$this->workModel->archiveTask($id, $this->workContext())) {
            return array('success' => false, 'message' => 'Task could not be archived or access denied.');
        }
        return array('success' => true, 'message' => 'Task archived successfully.', 'id' => $id);
    }

    private function actWorkSaveUpdate(): array
    {
        $p = $this->request->getPost();
        $rawStatus = (string) ($p['work_status'] ?? ($p['status'] ?? 'working'));
        $statusMap = array(
            'planned' => 'working',
            'todo' => 'working',
            'in_progress' => 'working',
            'deferred' => 'paused',
        );
        $rawStatus = $statusMap[$rawStatus] ?? $rawStatus;

        $steps = $this->cleanLong((string) ($p['steps'] ?? ''));
        $stepsJson = trim((string) ($p['steps_json'] ?? ''));
        if ($stepsJson !== '') {
            $decodedSteps = json_decode($stepsJson, true);
            if (is_array($decodedSteps)) {
                $steps = $decodedSteps;
            }
        }

        $projectId = (int) ($p['project_id'] ?? 0);
        $taskId = (int) ($p['task_id'] ?? 0);
        $title = $this->clean((string) ($p['update_title'] ?? ($p['summary'] ?? '')));
        if ($taskId <= 0 && $projectId <= 0) {
            return array('success' => false, 'message' => 'Please select project/schedule or task.');
        }
        if ($title === '') {
            return array('success' => false, 'message' => 'Update title is required.');
        }

        $data = array(
            'id' => (int) ($p['id'] ?? 0),
            'project_id' => $projectId,
            'task_id' => $taskId,
            'member_id' => (int) ($p['member_id'] ?? 0),
            'update_date' => $this->date((string) ($p['update_date'] ?? ($p['work_date'] ?? date('Y-m-d'))), date('Y-m-d')),
            'update_title' => $title,
            'work_status' => $this->allowed($rawStatus, array('working', 'testing', 'review', 'blocked', 'paused', 'completed'), 'working'),
            'is_today_focus' => (int) ($p['is_today_focus'] ?? 1) === 1 ? 1 : 0,
            'completion_percent' => $p['completion_percent'] ?? ((string) ($p['status'] ?? '') === 'completed' ? 100 : null),
            'hours_spent' => (float) ($p['hours_spent'] ?? 0),
            'planned_hours' => (float) ($p['planned_hours'] ?? 0),
            'today_task' => $this->cleanLong((string) ($p['today_task'] ?? ($p['summary'] ?? ''))),
            'progress_note' => $this->cleanLong((string) ($p['progress_note'] ?? ($p['summary'] ?? ''))),
            'achievements' => $this->cleanLong((string) ($p['achievements'] ?? ($p['completed_tasks'] ?? ''))),
            'blockers' => $this->cleanLong((string) ($p['blockers'] ?? '')),
            'tomorrow_plan' => $this->cleanLong((string) ($p['tomorrow_plan'] ?? ($p['next_plan'] ?? ''))),
            'steps' => $steps,
            'sync_project_progress' => (int) ($p['sync_project_progress'] ?? 1) === 1 ? 1 : 0,
            'review_status' => 'submitted',
        );

        $updateId = $this->workModel->saveUpdate($data, $this->workContext());
        if ($updateId <= 0) {
            return array('success' => false, 'message' => 'Unable to save work update.');
        }

        return array('success' => true, 'message' => 'Work update saved successfully.', 'update_id' => $updateId);
    }

    private function actWorkUpdates(): array
    {
        $filters = array(
            'project_id' => $this->request->getGet('project_id'),
            'task_id' => $this->request->getGet('task_id'),
            'member_id' => $this->request->getGet('member_id'),
            'date' => $this->request->getGet('date'),
            'from' => $this->request->getGet('from'),
            'to' => $this->request->getGet('to'),
            'today' => $this->request->getGet('today'),
            'search' => $this->request->getGet('search'),
        );
        return array('success' => true, 'updates' => $this->workModel->getUpdates($filters, $this->workContext()));
    }

    private function actWorkReviewUpdate(): array
    {
        $id = (int) ($this->request->getPost('id') ?? 0);
        $status = $this->allowed((string) ($this->request->getPost('review_status') ?? 'approved'), array('submitted', 'approved', 'rejected'), 'approved');
        $note = $this->cleanLong((string) ($this->request->getPost('review_note') ?? ''));
        if (!$this->workModel->reviewUpdate($id, $status, $note, $this->workContext())) {
            return array('success' => false, 'message' => 'Review could not be saved.');
        }
        return array('success' => true, 'message' => 'Review saved successfully.', 'id' => $id);
    }

    private function actWorkDaily(): array
    {
        $from = $this->date((string) ($this->request->getGet('from') ?? date('Y-m-d')), date('Y-m-d'));
        $to = $this->date((string) ($this->request->getGet('to') ?? $from), $from);
        $rows = $this->workModel->getAttendanceWorkDaily(array('from' => $from, 'to' => $to), $this->workContext());
        return array('success' => true, 'rows' => $rows, 'daily' => $rows);
    }

    private function actWorkWeek(): array
    {
        $start = $this->date((string) ($this->request->getGet('from') ?? date('Y-m-d', strtotime('monday this week'))), date('Y-m-d', strtotime('monday this week')));
        $end = $this->date((string) ($this->request->getGet('to') ?? date('Y-m-d', strtotime($start . ' +6 days'))), date('Y-m-d', strtotime($start . ' +6 days')));
        return array(
            'success' => true,
            'from' => $start,
            'to' => $end,
            'daily' => $this->workModel->getAttendanceWorkDaily(array('from' => $start, 'to' => $end), $this->workContext()),
            'weekly' => $this->workModel->getAttendanceWorkWeekly(array('from' => $start, 'to' => $end), $this->workContext()),
        );
    }

    private function actWorkMonth(): array
    {
        $year = (int) ($this->request->getGet('year') ?? date('Y'));
        $month = (int) ($this->request->getGet('month') ?? date('m'));
        if ($year < 2000 || $year > 2100) {
            $year = (int) date('Y');
        }
        if ($month < 1 || $month > 12) {
            $month = (int) date('m');
        }
        $monthly = $this->workModel->getAttendanceWorkMonthly(array('year' => $year, 'month' => $month), $this->workContext());
        return array('success' => true, 'rows' => $monthly, 'monthly' => $monthly);
    }

    private function actWorkCompare(): array
    {
        return array('success' => true, 'comparison' => $this->workModel->getComparison($this->workContext()));
    }

    private function actWorkDashboard(): array
    {
        return array('success' => true, 'dashboard' => $this->workModel->getDashboard($this->workContext()));
    }

    private function actWorkCommandCenter(): array
    {
        $ctx = $this->workContext();
        $filters = array(
            'period'     => trim((string) ($this->request->getGet('period')     ?? 'today')),
            'from'       => trim((string) ($this->request->getGet('from')       ?? '')),
            'to'         => trim((string) ($this->request->getGet('to')         ?? '')),
            'week_of'    => trim((string) ($this->request->getGet('week_of')    ?? '')),
            'day'        => trim((string) ($this->request->getGet('day')        ?? '')),
            'month'      => trim((string) ($this->request->getGet('month')      ?? '')),
            'year'       => trim((string) ($this->request->getGet('year')       ?? '')),
            'emp_id'     => (int) ($this->request->getGet('emp_id')     ?? 0),
            'project_id' => (int) ($this->request->getGet('project_id') ?? 0),
            'work_type'  => trim((string) ($this->request->getGet('work_type')  ?? '')),
            'status'     => trim((string) ($this->request->getGet('status')     ?? '')),
            'search'     => trim((string) ($this->request->getGet('search')     ?? '')),
        );
        return $this->workModel->getCommandCenter($filters, $ctx);
    }

    private function actWorkEmployeeDetail(): array
    {
        $filters = array(
            'period'  => trim((string) ($this->request->getGet('period')  ?? 'today')),
            'from'    => trim((string) ($this->request->getGet('from')    ?? '')),
            'to'      => trim((string) ($this->request->getGet('to')      ?? '')),
            'week_of' => trim((string) ($this->request->getGet('week_of') ?? '')),
            'day'     => trim((string) ($this->request->getGet('day')     ?? '')),
            'month'   => trim((string) ($this->request->getGet('month')   ?? '')),
            'year'    => trim((string) ($this->request->getGet('year')    ?? '')),
        );

        $userId = (int) ($this->request->getGet('user_id') ?? $this->uid());
        return $this->workModel->getEmployeeWorkDetail($userId, $filters, $this->workContext());
    }

    private function uid(): int
    {
        return (int) (session()->get('attend_uid') ?? 0);
    }

    private function auth(callable $cb): array
    {
        if ($this->uid() <= 0) {
            return array('success' => false, 'message' => 'Session expired. Please login again.');
        }
        return $cb();
    }

    private function authEdit(callable $cb): array
    {
        if (!session()->get('attend_can_edit')) {
            return array('success' => false, 'message' => 'No edit permission.');
        }
        return $cb();
    }

    private function authAnalytics(callable $cb): array
    {
        if (!session()->get('attend_can_analytics')) {
            return array('success' => false, 'message' => 'No analytics permission.');
        }
        return $cb();
    }

    private function authAddEmp(callable $cb): array
    {
        if (!session()->get('attend_can_add_emp')) {
            return array('success' => false, 'message' => 'Only HR, Manager or Admin can add employees.');
        }
        return $cb();
    }

    private function workContext(): array
    {
        $sess = session();
        return $this->workModel->makeContext(array(
            'user_id' => (int) ($sess->get('attend_uid') ?? 0),
            'role' => (string) ($sess->get('attend_role') ?? 'employee'),
            'department_id' => (int) ($sess->get('attend_department_id') ?? 0),
            'can_edit' => (bool) ($sess->get('attend_can_edit') ?? false),
            'can_analytics' => (bool) ($sess->get('attend_can_analytics') ?? false),
        ));
    }

    private function role(string $r): string
    {
        $r = strtolower(trim(str_replace(array(' ', '-'), '_', $r)));
        return ($r === 'team_lead') ? 'teamlead' : $r;
    }

    private function date(string $d, string $fb): string
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($d)) ? trim($d) : $fb;
    }

    private function status(string $s): string
    {
        $s = strtolower(trim(str_replace(array(' ', '-'), '_', $s)));
        return in_array($s, array('present', 'absent', 'half_day', 'leave', 'work_from_home', 'holiday'), true) ? $s : '';
    }

    private function dt(string $date, string $time): ?string
    {
        $t = trim($time);
        if ($t === '' || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $t)) {
            return null;
        }
        return $date . ' ' . (strlen($t) === 5 ? $t . ':00' : $t);
    }

    private function ua(): string
    {
        $a = $this->request->getUserAgent();
        if (is_object($a) && method_exists($a, 'getAgentString')) {
            return substr($a->getAgentString(), 0, 255);
        }
        if (is_string($a)) {
            return substr($a, 0, 255);
        }
        return '';
    }

    private function clean(string $value): string
    {
        return strip_tags(trim($value));
    }

    private function cleanLong(string $value): string
    {
        return strip_tags(trim($value));
    }

    private function allowed(string $value, array $allowed, string $default): string
    {
        $value = trim($value);
        return in_array($value, $allowed, true) ? $value : $default;
    }

    private function j(array $payload, int $status = 200)
    {
        $payload['csrf_hash'] = csrf_hash();
        return $this->response
            ->setStatusCode($status)
            ->setHeader('Content-Type', 'application/json; charset=UTF-8')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}