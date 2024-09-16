# Malwarebytes Technical Assigment

## Development Plan
1. The functionality should be implemented as a plugin.
2. The plugin should depend on the Weglot plugin, as Weglot provides configured settings such as the `api_key`.  
   (Although it could be created as an independent plugin, but it will most likely be used as an extension of the Weglot plugin.)
3. Plugin structure:
    - `index.php` - core file.
    - `includes/admin.php` - contains functionality for the admin page, including string input and a results table.
    - `includes/api.php` - contains endpoints for internal JS fetch requests.
    - `includes/cron.php` - handles the daily cron job task.
    - `includes/translator.php` - class with generic translation functionality.
4. The plugin should have its own custom table in the database with the following fields:
    - `id`
    - `slug`
    - `text`
    - `lang`
    - `url`
    - `status`
    - `parent_id` (ID of the original string; should only be set for translated records).
5. PHPUnit tests should be implemented.

## Translated String Link Example:

- `/translation/{{ SLUG }}/{{ LANG }}`
- `/translation/hello/pt-br`

Each string should have a unique slug. The combination of `slug` and `lang` will serve as a unique key in the database table.


--- 
## TODO
   - Complete and test the cron job functionality.
   - Test Weglot API requests and update plugin functionality based on the responses.
   - Verify all validations have been added.
   - Clean up the codebase, if needed.
   - Add PHPUnit tests.
### stage 2:
   - Add search-by-slug/text functionality to the admin page.
   - Add update/delete functionality for translated strings.
   - Update the styles of elements on the plugin admin page.
   - Add pagination to results table.
---


## User Story

As a software engineer, I want to use Wordpress to enter strings of English text to be translated into 9 languages. The translated strings will be made available to a frontend JavaScript application as a JSON response from a URL.

## Acceptance Criteria

- Provide a user interface in Wordpress for users to enter strings of English text.
- As each string is added or according to a daily schedule, the strings will be translated into the following languages using the Weglot API:
  - Brazilian Portuguese
  - Dutch
  - French
  - German
  - Italian
  - Japanese
  - Polish
  - Portuguese
  - Spanish
  - Russian
- The translated strings should be made available via specific URLs, where each URL corresponds to one of the languages.
