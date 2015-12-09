Magento 2 Composer Extension Repository
=====

This extension works as a private Mangento 2 composer repository manager for extension developers that sell Magento 2 extensions and want to offer easy composer installation.

Requirements:
=====
- Magento 1.x
- Composer
- Satis

How does it work
=====

Using Satis the composer.json and packages will be generated and stores on the server. For every package ordered thru the webshop it checks if the ordered products are M2 packages (based on the product_id) and inserts this in the customer_packages table so that user will have access to the bought package. If there is no customer_auth key available it will be generated.

1. With the auth key+secret the customer can install the package from the command file with Composer. For this Composer will request the /packages.json from the repository, based on the used key/secret the customer_id is checked for available packages and build the output.
2. When a download is requested it requests /composer/download/file/ with the parameters (m/[package_name]/h/[package_hash]/v/[normalized_version, again based on the used key+secret access to the file and version is checked, if allowed file is send from the [satis archive directory] to the user
3. Thru the `notify-batch` URL the installation of a package is recorded for the user (stores IP, Package, Version and User ID)

TODO
=====

- Send e-mail with instructions on how to install the package
  - Add the repository (composer config repositories.[name] composer [repo url]
  - Optionally add auth for repo
  - Install package (composer require [package name])
- Backend forms to add/edit packages
- Statistics screen for downloads
- Check on generated key+secret pair to be unique

Configuration:
=====

Satis installation
-----

Install Satis outside your Magento Webroot but accesable from the Magento web user:

`php composer.phar create-project composer/satis --stability=dev --keep-vcs [path]`

The Satis installation doesn't need to be access from the web, it is only used to collect the composer json files and generate the downloadable files

Store configuration
-----

The repository is a dedicated store within Magento. Its best to use a separate Store and Store View within the same website. For the store select an empty Root Catalog. To disable the rendering of the default Magento URL's set the template package to composerrepo, this has an empty page.xml to disable the rendering of every page. 

Repository Configuration:
-----

After installation of the extension goto: `System=>Configuration=>Genmato=>Composer Repo` and complete the following fields:

[Configuration]
Repository name: This name is used for the composer config repositories.[name] command (for example: genmato)
Repository URL: The url that serves the Repository (for example: https://repo.genmato.com)
Include dev-master: Optionally enable the option to allow access to the dev-master package (disabled by default)
Update period: The period in months that user can get free updates to a newer release, when the period ends the user only has access to the versions release before. Not possible when dev-master is enabled, leave empty for unlimited updates.

[Configuration on store level]
Enabled: When enabled on store level it generates a url-rewrite to request the packages.json

[Satis Configuration]
Satis command path: Path to the Satis executable (for example: /var/www/satis/bin/satis)
Satis config path: Path to the satis.json configuration file (for example: /var/www/satis/satis.json)
Name: Repository name (used for satis.json)
Homepage URL: Repository URL (used for satis.json)
Output directory: Path the the Satis web directory (for example: /var/www/satis/web)

[Satis Archive]
Format: Export format (zip or tar) of the packages (for example: zip)
Absolute Directory: Path to where the downloaded packages should be places (for example: /var/www/satis/packages/

M2 Package configuration:
=====

The Magento 2 package/extensions should be stored in a private repository, it is important that the account where Satis is running from has access to download from this repository.

Adding M2 packages
=====

Currently the backend forms are not yet ready, for now it is possible to add a package to the table `genmato_composerrepo_packages`:
- createdate: Creation date/time
- status: 0:Disabled, 1:Enabled, 2:Free (free can be used for required libraries, these packages are always available to the user to install)
- product_id: Matching product entity_id for the ordered item
- name: composer package name (for example: genmato/multistoresearchfields)
- Description: Optional name of the extension (used in the customer account listing)
- repository_url: Git URL to the repository (for example: git@github.com:Genmato/M2_MultistoreSearchFields.git)
- repository_options: json format of options available for the repository in the satis.json (see https://getcomposer.org/doc/articles/handling-private-packages-with-satis.md for details)
- package_json: Leave empty, will be generated by Satis
- version: Leave empty, will be generated by Satis

Building the repository data
=====

When the configuration and packages are ready the configration can be build with:

`php -f [magento-dir]/shell/composerrepo.php -- update --store_id [store_id]`

This command can also be scheduled to run daily (or any frequency you prefer) and automatically update the repository data


