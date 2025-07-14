# Project Name

This is a starter template for the application, intended to provide a foundation for further development and customization.

## Table of contents

- [Local setup using DDEV](#local-setup-using-ddev)
- [GIT: Updating local environment & Merge request Workflow](#git-updating-local-environment--merge-request-workflow)
  - Update Local environment
  - Create a Merge Request

## Local setup using DDEV

In order to run this site you'll need the following dependencies:

- DDEV: <https://ddev.com/>

Once the dependencies are installed, you can begin the project setup.

1. Clone the repo to your local workspace.
    ```
    git clone <ssh-link>
    ```
2. Run ddev setup
    ```
    ddev config
    ```
3. Start the ddev project
    ```
    ddev start
    ```
4. Install Composer dependencies.
    ```
    ddev composer install
    ```
5. Import the latest database.
    ```
    ddev import-db --file=db.sql.gz
    ```
7. Ensure that site configs are synced.
    ```
    ddev drush cim -y
    ddev drush updb -y
    ddev drush cr
    ```

Once complete, you can access the site at <https://example.ddev.site/>.

## GIT: Updating local environment & Merge request Workflow

### To update Local environment:

1. Checkout to the develop branch:
   ```bash
   git checkout develop
   ```
2. Pull the latest changes:
   ```bash
   git pull origin develop
   ```
3. Install composer dependencies:
   ```bash
   ddev composer install
   ```
4. Import configurations and execute database updates:
   ```bash
   ddev drush cim -y
   ddev drush updb -y
   ```
5. Clear Drupal cache:
   ```bash
   ddev drush cr
   ```

### To Create a Merge Request (MR):

1. Consider develop branch as a base branch:
   ```bash
   git checkout develop
   ```
2. Pull the latest changes:
   ```bash
   git pull origin develop
   ```
3. For new features, create a feature branch (Format: `feature/<ticketnumber>`):
   ```bash
   git checkout -b feature/<ticketnumber>
   ```
4. For bugs, create a bug branch (Format: `bugfix/<ticketnumber>`):
   ```bash
   git checkout -b bugfix/<ticketnumber>
   ```
5. Do your updates.
6. Check code quality using PHP_CodeSniffer:
   ```bash
   ddev vendor/bin/phpcs --standard=Drupal <path/to/file-or-folder>
   ```
7. Stage your changes:
   ```bash
   git add <files>
   ```
8. Commit your changes:
   ```bash
   git commit -m "#<ticket number>: <short message>"
   ```
9. Push your commits:
   ```bash
   git push origin <feature OR bugfix>/<ticketnumber>
   ```
10. Create a merge request (MR):
   Open a merge/pull request on your Git hosting platform targeting the `develop` branch.
