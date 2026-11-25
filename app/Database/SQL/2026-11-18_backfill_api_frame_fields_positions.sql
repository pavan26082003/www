-- Safe backfill for older default-API rows that lost start/end/name
-- because inserts used non-existent column names.
-- Does NOT overwrite rows that already have start_byte set.
-- Assumes contiguous packing by row_sequence within each frame (0-based).

-- 1) name from field_description where missing
UPDATE api_frame_fields
SET name = field_description
WHERE (name IS NULL OR name = '')
  AND field_description IS NOT NULL
  AND field_description <> '';

-- 2) Reconstruct start_byte / end_byte for rows still missing positions.
-- MySQL 8+ window functions.
UPDATE api_frame_fields ff
JOIN (
  SELECT
    id,
    (SUM(CAST(IFNULL(NULLIF(no_of_bytes, ''), '1') AS UNSIGNED))
      OVER (PARTITION BY frame_id ORDER BY row_sequence, id)
     - CAST(IFNULL(NULLIF(no_of_bytes, ''), '1') AS UNSIGNED)
    ) AS calc_start,
    (SUM(CAST(IFNULL(NULLIF(no_of_bytes, ''), '1') AS UNSIGNED))
      OVER (PARTITION BY frame_id ORDER BY row_sequence, id)
     - 1
    ) AS calc_end
  FROM api_frame_fields
  WHERE field_level = 'bytewise'
) x ON x.id = ff.id
SET
  ff.start_byte = CAST(x.calc_start AS CHAR),
  ff.end_byte   = CAST(x.calc_end AS CHAR)
WHERE ff.field_level = 'bytewise'
  AND (ff.start_byte IS NULL OR ff.start_byte = '')
  AND (ff.end_byte IS NULL OR ff.end_byte = '')
  AND ff.no_of_bytes IS NOT NULL
  AND ff.no_of_bytes <> '';

-- Verify:
-- SELECT COUNT(*) AS still_missing_start
-- FROM api_frame_fields
-- WHERE field_level='bytewise' AND (start_byte IS NULL OR start_byte='');
