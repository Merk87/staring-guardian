Staring-Guardian - Another MerkCorp App.
======================

Contributors
======================
Merkury

Requirements
======================
* PHP ^7.0
* MariaDB (MySQL) 10.1
* Composer

So... what is this?
======================
Staring guardian is a simple monitor app to connect, retrieve and display the 
deployment status of projects in your jenkins machine.

The project is articulated to be a public/private single page, displaying
your jenkins' works last build status with a semaphore strategy.

Oh by the way, the curl calls are not error proof, if you don't get a jenkins object, it will break the world, next
version will fix that :)

Thanks
======================
Thanks to BrokenPixel (John James) to let me use the CurlBundle.
 
TODOs
======================

* Control on bad responses
* Individual status query.

