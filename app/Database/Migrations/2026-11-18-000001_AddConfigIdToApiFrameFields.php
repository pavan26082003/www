<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Denormalize config_id onto api_frame_fields so fields can be queried
 * by API config without joining through api_frames.
 *
 * Safe / additive: nullable column + backfill + index. Existing FKs unchanged.
 */
class AddConfigIdToApiFrameFields extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('api_frame_fields')) {
            return;
        }

        if (!$this->db->fieldExists('config_id', 'api_frame_fields')) {
            $this->forge->addColumn('api_frame_fields', [
                'config_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'frame_id',
                ],
            ]);
        }

        // Backfill from parent frame.
        if ($this->db->tableExists('api_frames') && $this->db->fieldExists('config_id', 'api_frame_fields')) {
            $this->db->query(
                'UPDATE api_frame_fields ff
                 INNER JOIN api_frames f ON f.id = ff.frame_id
                 SET ff.config_id = f.config_id
                 WHERE ff.config_id IS NULL OR ff.config_id = 0 OR ff.config_id <> f.config_id'
            );
        }

        // Index for model/API lookups (ignore if already present).
        try {
            $this->db->query('CREATE INDEX idx_api_frame_fields_config_id ON api_frame_fields (config_id)');
        } catch (\Throwable $e) {
            // index may already exist
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('api_frame_fields')) {
            return;
        }

        if ($this->db->fieldExists('config_id', 'api_frame_fields')) {
            try {
                $this->db->query('DROP INDEX idx_api_frame_fields_config_id ON api_frame_fields');
            } catch (\Throwable $e) {
                // ignore
            }
            $this->forge->dropColumn('api_frame_fields', 'config_id');
        }
    }
}
