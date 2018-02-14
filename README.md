QUICK SLPK SERVER PHP

Minimalist web server engine to publish OGC SceneLayerPackage (.slpk) to Indexed 3d Scene Layer (I3S) web service.
(Usable with classic Apache +PHP server)  [See also a python version in my repos]

Why this projects? Publishing I3S service to Portal for ArcGIS requires to federated ArcGIS Server ... with this server, you can bypass this step and keep going with your non federated arcgis server.

How to use:

    Unzip the project into an apache web accessible folder
    
    Place .SLPK into a folder (default: "./slpk") [You can create SLPK with ArcGIS pro and some other softwares]

    Configure the script (index.php / map.php):
        slpk folder

    Open browser to "host:port/{webfolder}/index.php"

    Index page let you access your SLPK as I3S services

    Also provide an intern viewer for test

    You can use your I3S services on arcgis online, portal for arcgis, arcgis javascript API, ... simply use ther service url: {host}:{port}/{webfolder}/server/{slpk name .slpk}/SceneServer

How to:
    Configure Viewer page: map.php

Sources:

    php 5.x
    I3S Specifications: https://github.com/Esri/i3s-spec
    Arcgis Javascript API >=4.6

Autor: RIVIERE Romain Date: 12/02/2018 Licence: GNU GPLv3
