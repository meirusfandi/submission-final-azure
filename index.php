<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Azure Cloud Developer Academy</title>
</head>
<body>
    <h2>Upload New Image Source</h2>
    <hr>
    <form action="index.php" method="post" enctype="multipart/form-data">
        <label for="fileUpload">File Source</label><br><br>
        <input type="file" name="file" id="file"><br><br>
        <input type="submit" name="upload" value="Upload Image"> | 
        <input type="submit" name="load" value="Load File">
    </form>

    <!-- PHP Section -->
    <?php 
        require_once 'vendor/autoload.php';
        // require_once './random_string.php';
        
        use MicrosoftAzure\Storage\Blob\BlobRestProxy;
        use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
        use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
        use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
        use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
        
        $connectionString = "DefaultEndpointsProtocol=https;AccountName=meirusfandiwev;AccountKey=vwhIwbU1kaFKEZMFWTd5ng21ux0PA8P8XRgUgo6atp8xbKPYFStk5vz+7/lTIG8SyZ/37LGfYqQxqbsX/EIwCQ==;EndpointSuffix=core.windows.net";
        
        // Create blob client.
        $blobClient = BlobRestProxy::createBlobService($connectionString);
        
        // Create container options object.
        $createContainerOptions = new CreateContainerOptions();
        
        $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
        
        // Set container metadata.
        $createContainerOptions->addMetaData("key1", "value1");
        $createContainerOptions->addMetaData("key2", "value2");
            
        $containerName = "meirusfandi";
        
        // $blobClient->createContainer($containerName, $createContainerOptions);
        
        if (isset($_POST['upload'])){
            
            try {
                // Getting local file so that we can upload it to Azure
                $filename = strtolower($_FILES['file']['name']);
                $filecontent = fopen($_FILES['file']['tmp_name'], "r");
                
                # Upload file as a block blob
                echo "Uploading File: ".PHP_EOL;
                echo $filename;
                echo " was Successfully<br />";

                //Upload blob
                $blobClient->createBlockBlob($containerName, $filename, $filecontent);

            } catch(ServiceException $e){
                $code = $e->getCode();
                $error_message = $e->getMessage();
                echo $code.": ".$error_message."<br />";
            } catch(InvalidArgumentTypeException $e){
                $code = $e->getCode();
                $error_message = $e->getMessage();
                echo $code.": ".$error_message."<br />";
            }
        } else if (isset($_POST['load'])){

            // List blobs.
            $listBlobsOptions = new ListBlobsOptions();
            $listBlobsOptions->setPrefix("");

            echo "<hr>";
            echo "<h2>Files on Blob Storage : </h2>";
            echo '<table border="1">';
            echo "<thead>";
            echo "<th>No. </th>";
            echo "<th>File Name</th>";
            echo "<th>File Url</th>";
            echo "<th>Preview</th>";
            echo "<th>Action</th>";
            echo "</thead>";

            echo "<tbody>";
            $i = 0;
            do {
                $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
                
                foreach ($result->getBlobs() as $blobFile) {
                    echo "<tr>";
                        echo "<td>".++$i."</td>";
                        echo "<td>".$blobFile->getName()."</td>";
                        echo '<td width="200px">';
                            echo $blobFile->getUrl();
                        echo '</td>';
                        $url = $blobFile->getUrl();
                        echo '<td>';
                            echo '<div id="image" style="width:220px;">';
                                echo '<img src="'.$url.'" width="200" />';
                            echo "</div>";
                        echo '</td>';
                        echo "<td>";
                            echo '<form action="analyze.php" method="post">';
                                echo '<input type="hidden" name="url" value="'.$url.'">';
                                echo '<input type="submit" name="analyze" value="Analyze">';
                            echo "</form>";
                        echo "</td>";
                    echo "</tr>";
                }
                $listBlobsOptions->setContinuationToken($result->getContinuationToken());
            } while($result->getContinuationToken());
            echo "</tbody>";
            echo "</table>";
        }
    ?>
    
</body>
</html>