### Detail module information

1. Namespace >> **modules/flexibleconfig**
2. Zip Archive source >> 
    https://github.com/aalfiann/reSlim-modules-flexibleconfig/archive/master.zip

### How to Integrate this module into reSlim?

1. Download zip then upload to reSlim server to the **modules/**
2. Extract zip then you will get new folder like **reSlim-modules-flexibleconfig-master**
3. Rename foldername **reSlim-modules-flexibleconfig-master** to **flexibleconfig**
4. Done

### How to Integrate this module into reSlim with Packager?

1. Make AJAX GET request to >>
    http://**{yourdomain.com}**/api/packager/install/zip/safely/**{yourusername}**/**{yourtoken}**/?lang=en&source=**{zip archive source}**&namespace=**{modul namespace}**

### How to update?

1. Your data config is saved into data.sqlite3, so make sure you keep this file before update this module.
2. Replace the data.sqlite3 with your old data.sqlite3 file
3. Done