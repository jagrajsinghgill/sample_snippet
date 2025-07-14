# <a href="https://github.com/jagrajsinghgill/sample_snippet/blob/main/ManageVersions.php">Sample Code Snippet</a>

This README file provides context and explains the use case of the included <a href="https://github.com/jagrajsinghgill/sample_snippet/blob/main/ManageVersions.php">code snippet</a>.

## Table of contents

- [What does it do?](#what-does-it-do)
- [Why was this approach used?](#why-was-this-approach-used)

## What does it do?

- This is a custom controller that renders a dedicated admin page.
- The controller is located at: `/modules/custom/sample_module/src/Controller/ManageVersions.php`
- The page is accessible via a route defined in the module's `sample_module.routing.yml` file.
- The purpose of this page is to display multiple versions of a specific application.
- From this interface, administrators can:
  - View all previously published versions along with their respective publish timestamps.
  - Publish a new version.
  - Preview any version of the application.
  - Access a review page that displays all associated content for verification before publishing.

## Why was this approach used?

- Although Views could have been used to build this page, a custom controller was chosen due to specific functional requirements.
- The page needed to handle custom configuration logic to support comparison between different versions.
- Several helper functions were required to manage version-related data and conditional logic.
- Based on this logic, the page includes conditionally enabled/disabled operational links, which could not have been effectively handled through Views alone.

**NOTE:** Please note that Module and some function/field names have been intentionally changed to maintain project confidentiality. This is a sample code snippet extracted from a fully functional module and is intended for illustrative purposes only.
