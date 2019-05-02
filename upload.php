<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upload Image</title>
</head>
<body>

    <!-- PHP section -->
    <?php
        require_once 'vendor/autoload.php';
        
        use MicrosoftAzure\Storage\Blob\BlobRestProxy;
        use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
        use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
        use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
        use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

        $connect = "DefaultEndpointsProtocol=https;AccountName=meirusfandiwev;AccountKey=vwhIwbU1kaFKEZMFWTd5ng21ux0PA8P8XRgUgo6atp8xbKPYFStk5vz+7/lTIG8SyZ/37LGfYqQxqbsX/EIwCQ==;EndpointSuffix=core.windows.net";
        $containername = "meirusfandi";
        $blobclient = BlobRestProxy::createBlockBlob($connect);

        if (isset($_POST['upload'])){
            try {
                $filename = strtolower($_FILES['file']['name']);
                $filecontent = fopen($_FILES['file']['tmp_name'], "r");
                // upload blob
                $blobs->createBlockBlob($containername, $filename, $filecontent);
            }catch(ServiceException $e){
                $code = $e->getCode();
                $error_message = $e->getMessage();
                echo $code.": ".$error_message."<br />";
            }catch(InvalidArgumentTypeException $e){
                $code = $e->getCode();
                $error_message = $e->getMessage();
                echo $code.": ".$error_message."<br />";
            }
        }
    ?>
    <h2>Upload Image to Storage</h2>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="image">
        <input type="submit" name="upload" value="Upload Image">
    </form>
</body>
</html>