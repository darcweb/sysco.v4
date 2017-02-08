<!DOCTYPE html>
<html lang="pt-br">
    <head>

        <meta charset="utf-8"/>

        <title>{{ echo $this->sysco->params['sysco']['title']; }}</title>

        <!-- Vendor CSS -->
        <link href="{{ echo $this->sysco->request->appviewsurl }}theme/vendors/bower_components/animate.css/animate.min.css" rel="stylesheet">
        <link href="{{ echo $this->sysco->request->appviewsurl }}theme/vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css" rel="stylesheet">

        <!-- CSS -->
        <link href="css/app_1.min.css" rel="stylesheet">
        <link href="css/app_2.min.css" rel="stylesheet">

    </head>
    <body>
        
        <div>Hello world, i am application {{ echo $this->sysco->request->application; }}!</div>

        {{content}}

    </body>
</html>