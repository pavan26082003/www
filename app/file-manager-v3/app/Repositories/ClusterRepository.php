<?php
namespace App\Repositories;

use PDO;

class ClusterRepository extends Repository
{
    /**
     * Get cluster by slug for a specific user
     */
    public function getBySlug(int $userId, string $slug): ?array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, uc.user_id 
            FROM storage_clusters c
            INNER JOIN user_clusters uc ON c.id = uc.cluster_id
            WHERE uc.user_id = ? AND c.slug = ?
        ");
        $stmt->execute([$userId, $slug]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get default cluster for a user
     */
    public function getDefault(int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, uc.user_id 
            FROM storage_clusters c
            INNER JOIN user_clusters uc ON c.id = uc.cluster_id
            WHERE uc.user_id = ? AND c.is_default = 1
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get root folders for a cluster
     */
    public function getRootFolders(int $userId, int $clusterId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, name as display_name, 'folder' as type, created_at, 
                   is_starred, cluster_id, NULL as file_size, NULL as file_type
            FROM folders 
            WHERE user_id = ? AND cluster_id = ? AND parent_id IS NULL AND deleted_at IS NULL 
            ORDER BY name ASC
        ");
        $stmt->execute([$userId, $clusterId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all clusters for a user
     */
    public function getAll(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, uc.user_id 
            FROM storage_clusters c
            INNER JOIN user_clusters uc ON c.id = uc.cluster_id
            WHERE uc.user_id = ?
            ORDER BY c.is_default DESC, c.sort_order ASC, c.name ASC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function createDefaultClusters(int $userId): void
    {
        // First check if user already has clusters
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM user_clusters WHERE user_id = ?");
        $stmt->execute([$userId]);
        $count = (int)$stmt->fetchColumn();
        
        if ($count > 0) {
            return;
        }

        $clusters = [
            ['name' => 'My Drive', 'slug' => 'my-drive', 'is_default' => 1, 'sort_order' => 1],
            ['name' => 'Shared with Me', 'slug' => 'shared', 'is_default' => 0, 'sort_order' => 2],
            ['name' => 'Computers', 'slug' => 'computers', 'is_default' => 0, 'sort_order' => 3],
            ['name' => 'Projects', 'slug' => 'projects', 'is_default' => 0, 'sort_order' => 4],
            ['name' => 'Images', 'slug' => 'images', 'is_default' => 0, 'sort_order' => 5],
            ['name' => 'Videos', 'slug' => 'videos', 'is_default' => 0, 'sort_order' => 6],
            ['name' => 'Documents', 'slug' => 'documents', 'is_default' => 0, 'sort_order' => 7],
            ['name' => 'Archives', 'slug' => 'archives', 'is_default' => 0, 'sort_order' => 8],
            ['name' => 'Starred', 'slug' => 'starred', 'is_default' => 0, 'sort_order' => 9],
            ['name' => 'Trash', 'slug' => 'trash', 'is_default' => 0, 'sort_order' => 10]
        ];

        $stmt = $this->db->prepare("
            INSERT INTO storage_clusters (user_id, name, slug, is_default, sort_order) 
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($clusters as $cluster) {
            try {
                $stmt->execute([
                    $userId, 
                    $cluster['name'], 
                    $cluster['slug'], 
                    $cluster['is_default'],
                    $cluster['sort_order']
                ]);
            } catch (\PDOException $e) {
                // Skip duplicates
            }
        }
    }

    /**
     * Create global clusters if they don't exist
     */
    private function createGlobalClusters(): void
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM storage_clusters");
        $stmt->execute();
        $count = (int)$stmt->fetchColumn();
        
        if ($count > 0) {
            return; // Already exists
        }

        $clusters = [
            ['name' => 'My Drive', 'slug' => 'my-drive', 'is_default' => 1, 'sort_order' => 1],
            ['name' => 'Shared with Me', 'slug' => 'shared', 'is_default' => 0, 'sort_order' => 2],
            ['name' => 'Computers', 'slug' => 'computers', 'is_default' => 0, 'sort_order' => 3],
            ['name' => 'Projects', 'slug' => 'projects', 'is_default' => 0, 'sort_order' => 4],
            ['name' => 'Images', 'slug' => 'images', 'is_default' => 0, 'sort_order' => 5],
            ['name' => 'Videos', 'slug' => 'videos', 'is_default' => 0, 'sort_order' => 6],
            ['name' => 'Documents', 'slug' => 'documents', 'is_default' => 0, 'sort_order' => 7],
            ['name' => 'Archives', 'slug' => 'archives', 'is_default' => 0, 'sort_order' => 8],
            ['name' => 'Starred', 'slug' => 'starred', 'is_default' => 0, 'sort_order' => 9],
            ['name' => 'Trash', 'slug' => 'trash', 'is_default' => 0, 'sort_order' => 10]
        ];

        $stmt = $this->db->prepare("
            INSERT INTO storage_clusters (name, slug, is_default, sort_order) 
            VALUES (?, ?, ?, ?)
        ");

        foreach ($clusters as $cluster) {
            try {
                $stmt->execute([
                    $cluster['name'], 
                    $cluster['slug'], 
                    $cluster['is_default'],
                    $cluster['sort_order']
                ]);
            } catch (\PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Get clusters with counts for a user
     */
    public function getClustersWithCounts(int $userId): array
    {
        try {
            // First check if user has clusters
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM user_clusters WHERE user_id = ?");
            $stmt->execute([$userId]);
            $count = (int)$stmt->fetchColumn();
            
            if ($count === 0) {
                // Create default clusters for user
                $this->createDefaultClusters($userId);
            }
            
            $stmt = $this->db->prepare("
                SELECT 
                    c.*,
                    COALESCE((SELECT COUNT(*) FROM files f WHERE f.cluster_id = c.id AND f.user_id = ? AND f.deleted_at IS NULL), 0) as file_count,
                    COALESCE((SELECT COUNT(*) FROM folders f WHERE f.cluster_id = c.id AND f.user_id = ? AND f.deleted_at IS NULL), 0) as folder_count,
                    COALESCE((SELECT SUM(f.file_size) FROM files f WHERE f.cluster_id = c.id AND f.user_id = ? AND f.deleted_at IS NULL), 0) as total_size
                FROM storage_clusters c
                INNER JOIN user_clusters uc ON c.id = uc.cluster_id
                WHERE uc.user_id = ?
                ORDER BY c.is_default DESC, c.sort_order ASC, c.name ASC
            ");
            $stmt->execute([$userId, $userId, $userId, $userId]);
            $results = $stmt->fetchAll();
            
            // Add total_count for frontend
            foreach ($results as &$row) {
                $row['total_count'] = (int)$row['file_count'] + (int)$row['folder_count'];
            }
            
            return $results;
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function find(int $clusterId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM storage_clusters WHERE id = ?");
        $stmt->execute([$clusterId]);
        return $stmt->fetch() ?: null;
    }
}