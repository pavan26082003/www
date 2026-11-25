-- Additive / safe migration for local + production.
-- Purpose: put config_id on api_frame_fields so fields share the same API key
--          as api_product_configs.id and api_frames.config_id.
--
-- Run on DB: factor_db2 (local) / production equivalent.
-- Idempotent-ish: ADD COLUMN fails if already present — skip that step if so.

-- 1) Add column (skip if already exists)
ALTER TABLE api_frame_fields
  ADD COLUMN config_id BIGINT UNSIGNED NULL AFTER frame_id;

-- 2) Backfill from parent frames
UPDATE api_frame_fields ff
INNER JOIN api_frames f ON f.id = ff.frame_id
SET ff.config_id = f.config_id
WHERE ff.config_id IS NULL
   OR ff.config_id = 0
   OR ff.config_id <> f.config_id;

-- 3) Index for fast lookup by API config
-- (skip if index already exists)
CREATE INDEX idx_api_frame_fields_config_id ON api_frame_fields (config_id);

-- Optional FK (commented — enable only if you want hard referential integrity):
-- ALTER TABLE api_frame_fields
--   ADD CONSTRAINT fk_api_frame_fields_config
--   FOREIGN KEY (config_id) REFERENCES api_product_configs(id)
--   ON DELETE CASCADE ON UPDATE CASCADE;

-- Verify:
-- SELECT COUNT(*) AS total,
--        SUM(config_id IS NULL) AS missing_config_id
-- FROM api_frame_fields;
