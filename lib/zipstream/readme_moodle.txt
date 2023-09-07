Instructions to import ZipStream into Moodle:

1/ Download from https://github.com/maennchen/ZipStream-PHP/releases/

2/ Copy the LICENSE file and the src folder into the lib/zipstream folder

3/ Ensure any dependencies are also imported (eg psr/http-message and myclabs/php-enum).
   The dependencies will be listed in the "require" section of the library's composer.json file

  * 2023/09/07 - MDL-72935 - Lower the value of the CHUNKED_READ_BLOCK_SIZE const.
    Issue when downloading a large file through Moodle from S3 can result in errors such as:

    warning [STAT0023_22-23.zip]: 5268 extra bytes at beginning or within zipfile
    (attempting to process anyway)
    file #1: bad zipfile offset (local header sig): 5268

    Reducing the CHUNKED_READ_BLOCK_SIZE resolves this issue
