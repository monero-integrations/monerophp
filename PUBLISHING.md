# Publishing a release

This document describes the process of publishing a new version of the package.

1. Update the version in the `composer.json` file.
2. Commit the changes.
	-	```bash
		git commit -m "chore: bump version to <version>"
		```

		Where `<version>` is the new version number (e.g. `1.2.3`).
3. Create a new tag.
	-	```bash
		git tag v<version>
		```

		Where `<version>` is the new version number (e.g. `1.2.3`).
4. Push the changes.
	-	```bash
		git push origin master --tags
		```

A release will automatically be created.
