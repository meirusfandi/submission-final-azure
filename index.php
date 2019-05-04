<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Azure Cloud Developer Academy</title>
    <script src="jquery.min.js"></script>
</head>
<body>
    <script type="text/javascript">
        function processImage() {
            
            var subscriptionKey = "<subscriptionKey>";
    
            var uriBase = "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";
    
            // Request parameters.
            var params = {
                "visualFeatures": "Categories,Description,Color",
                "details": "",
                "language": "en",
            };
    
            // Display the image.
            var sourceImageUrl = document.getElementById("inputImage").value;
            document.querySelector("#sourceImage").src = sourceImageUrl;
    
            // Make the REST API call.
            $.ajax({
                url: uriBase + "?" + $.param(params),
    
                // Request headers.
                beforeSend: function(xhrObj){
                    xhrObj.setRequestHeader("Content-Type","application/json");
                    xhrObj.setRequestHeader(
                        "Ocp-Apim-Subscription-Key", subscriptionKey);
                },
    
                type: "POST",
    
                // Request body.
                data: '{"url": ' + '"' + sourceImageUrl + '"}',
            })
    
            .done(function(data) {
                // Show formatted JSON on webpage.
                $("#responseTextArea").val(JSON.stringify(data, null, 2));
            })
    
            .fail(function(jqXHR, textStatus, errorThrown) {
                // Display error message.
                var errorString = (errorThrown === "") ? "Error. " :
                    errorThrown + " (" + jqXHR.status + "): ";
                errorString += (jqXHR.responseText === "") ? "" :
                    jQuery.parseJSON(jqXHR.responseText).message;
                alert(errorString);
            });
        };
    </script>

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
        echo "on php section 1";
        require_once 'vendor/autoload.php';
        echo "on php section 2";
        use MicrosoftAzure\Storage\Blob\BlobRestProxy;
        use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
        use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
        use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
        use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
        use WindowsAzure\Blob\Models\ListContainersOptions;
        echo "on php section 3";
        $connectionString = "DefaultEndpointsProtocol=https;AccountName=meirusfandiwev;AccountKey=vwhIwbU1kaFKEZMFWTd5ng21ux0PA8P8XRgUgo6atp8xbKPYFStk5vz+7/lTIG8SyZ/37LGfYqQxqbsX/EIwCQ==;EndpointSuffix=core.windows.net";
        echo "on php section 4";
        // Create blob client.
        $blobClient = BlobRestProxy::createBlobService($connectionString);
        echo "on php section 5";
        // Create container options object.
        $createContainerOptions = new CreateContainerOptions();
        echo "on php section 6";
        $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
        echo "on php section 7";
        // Set container metadata.
        $createContainerOptions->addMetaData("key1", "value1");
        $createContainerOptions->addMetaData("key2", "value2");
        echo "on php section 8";
        $containerName = "meirusfandi";
        echo "on php section 9";

        // See if the container already exists.
        $listContainersOptions = new ListContainersOptions;
        echo "on php section 10";
        $listContainersOptions->setPrefix($containerName);
        echo "on php section 11";
        $listContainersResult = $blobRestProxy->listContainers($listContainersOptions);
        echo "on php section 12";
        $containerExists = false;
        echo "on php section 13";

        foreach ($listContainersResult->getContainers() as $container) {
            echo "on php section 14";
            if ($container->getName() == CONTAINERNAME) {
                echo "on php section 15";
                // The container exists.
                $containerExists = true;
                // No need to keep checking.
                break;
            }
        }
        echo "on php section 16";
        if (!$containerExists){
            // Create container.
            echo "on php section 17";
            $blobClient->createContainer($containerName, $createContainerOptions);
            echo "on php section 18";
        }

        echo "on php section";

        // if (isset($_POST['upload'])){
        //     try {
        //         // Getting local file so that we can upload it to Azure
        //         $filename = strtolower($_FILES['file']['name']);
        //         $filecontent = fopen($_FILES['file']['tmp_name'], "r");
                
        //         # Upload file as a block blob
        //         echo "Uploading File: ".PHP_EOL;
        //         echo $filename;
        //         echo " was Successfully<br />";

        //         //Upload blob
        //         $blobClient->createBlockBlob($containerName, $filename, $filecontent);

        //     } catch(ServiceException $e){
        //         $code = $e->getCode();
        //         $error_message = $e->getMessage();
        //         echo $code.": ".$error_message."<br />";
        //     } catch(InvalidArgumentTypeException $e){
        //         $code = $e->getCode();
        //         $error_message = $e->getMessage();
        //         echo $code.": ".$error_message."<br />";
        //     }
        // } else if (isset($_POST['load'])){

        //     // List blobs.
        //     $listBlobsOptions = new ListBlobsOptions();
        //     $listBlobsOptions->setPrefix("");

        //     echo "<hr>";
        //     echo "<h2>Files on Blob Storage : </h2>";
        //     echo "<table>";
        //     echo "<thead>";
        //     echo "<th>No. </th>";
        //     echo "<th>File Name</th>";
        //     echo "<th>File Url</th>";
        //     echo "<th>Preview</th>";
        //     echo "<th>Action</th>";
        //     echo "</thead>";

        //     echo "<tbody>";
        //     $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
        //     if (sizeof($result->getBlobs()) > 0) {
        //         $i = 0;
        //         foreach ($result->getBlobs() as $blobFile) {
        //             echo "<tr>";
        //             echo "<td>".++$i."</td>";
        //             echo "<td>".$blobFile->getName()."</td>";
        //             echo "<td>".$blobFile->getUrl()."</td>";
        //             echo '<td width="200" height="200"><img src="'.$blobFile->getUrl().'" alt=""></td>';
        //             echo "<td>";
        //             echo '<input type="hidden" name="inputImage" id="inputImage" value="'.$blobFile->getUrl().'">';
        //             echo '<button onclick="processImage()">Analyze</button>';
        //             echo "</td>";
        //             echo "</tr>";
        //         }
        //     } else {
        //         echo "<tr>";
        //         echo '<td colspan="5">No data on this blob</td>';
        //         echo "</tr>";
        //     }
        //     echo "</tbody>";
        //     echo "</table>";
            
        //     echo "<hr>";
        //     echo "<br><br>";
        //     echo "<h2>Analyzing Image Using Computer Vision</h2>";
        //     echo '<div id="wrapper" style="width:1020px; display:table;">';
        //         echo '<div id="jsonOutput" style="width:600px; display:table-cell;">';
        //             echo "Response:";
        //             echo "<br><br>";
        //             echo '<textarea id="responseTextArea" class="UIInput" style="width:580px; height:400px;"></textarea>';
        //         echo "</div>";
        //         echo '<div id="imageDiv" style="width:420px; display:table-cell;">';
        //             echo "Source image:";
        //             echo "<br><br>";
        //             echo '<img id="sourceImage" width="400" />';
        //         echo "</div>";
        //     echo "</div>";
        // }
    ?>
    
</body>
</html>