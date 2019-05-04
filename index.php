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
            
            var subscriptionKey = "2e2671970d6b469399ac05285a925f3e";
    
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
                    echo "<td>".$blobFile->getUrl()."</td>";
                    echo '<td width="200" height="200"><img src="'.$blobFile->getUrl().'" alt=""></td>';
                    echo "<td>";
                    echo '<input type="hidden" name="inputImage" id="inputImage" value="'.$blobFile->getUrl().'">';
                    echo '<button onclick="processImage()">Analyze</button>';
                    echo "</td>";
                    echo "</tr>";
                }
                $listBlobsOptions->setContinuationToken($result->getContinuationToken());
            } while($result->getContinuationToken());
            echo "</tbody>";
            echo "</table>";
            
            echo "<hr>";
            echo "<br><br>";
            echo "<h2>Analyzing Image Using Computer Vision</h2>";
            echo '<div id="wrapper" style="width:1020px; display:table;">';
                echo '<div id="jsonOutput" style="width:600px; display:table-cell;">';
                    echo "Response:";
                    echo "<br><br>";
                    echo '<textarea id="responseTextArea" class="UIInput" style="width:580px; height:400px;"></textarea>';
                echo "</div>";
                echo '<div id="imageDiv" style="width:420px; display:table-cell;">';
                    echo "Source image:";
                    echo "<br><br>";
                    echo '<img id="sourceImage" width="400" />';
                echo "</div>";
            echo "</div>";
        }
    ?>
    
</body>
</html>