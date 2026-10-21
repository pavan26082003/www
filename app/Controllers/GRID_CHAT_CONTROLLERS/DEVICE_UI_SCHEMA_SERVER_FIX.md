# Design Studio save: schema file cannot be written

**Site:** efactor.tech  
**Screen:** Design Studio → Save Changes  
**Example model:** EF-FAN-V2-D-18-9  
**Date:** 19 Oct 2026

## What the user sees

```
Theme saved, but the device UI schema could not be updated. Could not write schema part "controls". The schema storage directory is not writable.
```

The theme colors are stored. The device UI schema is not.

## What this is

The full schema is too large for one MySQL query on this server (`max_allowed_packet` cannot be raised). The save keeps the database value small. Any large section, starting with `controls`, is written as a JSON file. The database only stores a short pointer:

```
{"schemaStorage":"split-v1",...}
```

The phone and Design Studio still receive one schema, with the same keys. That structure is not changing.

The failure is the file write. On this server PHP often cannot create files under `writable/`. Image uploads already avoid that folder and use `uploads/` instead. The schema save now does the same.

## File to deploy

Replace this file on the server. Path is under the site root (the folder that contains `index.php`):

- `app/services/DeviceUiSchemaGenerator.php`

Do not edit the schema JSON. Do not put the full schema back into `device_ui_schemas.schema_json`.

`app/Models/view_page_model.php` is already the version that calls this file. Leave it in place. Upload it again only if that server copy is older and does not call `DeviceUiSchemaGenerator::package`.

## Where the large section is stored

The save tries these folders, in order, and uses the first one PHP can actually create a file in:

1. CodeIgniter `writable/` (`<site-root>/writable/`)
2. `<site-root>/app/writable/`
3. `<site-root>/uploads/`

The file path inside that folder is unchanged:

```
device_ui_schemas/<model-name>/<node-id>/controls.json
```

For the model in the report that is `EF-FAN-V2-D-18-9`. `controls.json` is often tens of megabytes. Later large sections use the same folder (`deviceConfig.json`, and so on).

If `writable/` is not writable, the file will be:

```
<site-root>/uploads/device_ui_schemas/EF-FAN-V2-D-18-9/<node-id>/controls.json
```

PHP creates that folder on save. A `.htaccess` in `device_ui_schemas` denies public download of the part files. Reading the schema still goes through PHP, which loads the parts and returns the original schema.

## How to confirm it is fixed

1. Upload `app/services/DeviceUiSchemaGenerator.php`.
2. Open Design Studio for the model and click **Save Changes**.
3. The message should be **UI created successfully.**
4. One of these files should exist and be non-empty:

   ```
   <site-root>/writable/device_ui_schemas/EF-FAN-V2-D-18-9/<node-id>/controls.json
   <site-root>/uploads/device_ui_schemas/EF-FAN-V2-D-18-9/<node-id>/controls.json
   ```

5. In MySQL, `device_ui_schemas.schema_json` for that model should stay a short value starting with `{"schemaStorage":"split-v1"`. It must not be the full schema document.

If it still fails, the message now lists every folder PHP tried. Those folders need to be writable by the PHP user, and the account quota must allow a file of at least 100MB.
