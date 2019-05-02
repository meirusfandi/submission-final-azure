<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Final Submission | Azure Developer Academy</title>
    <script src="jquery.min.js"></script>
</head>
<body>
    <h2>Image List From Blob Storage</h2>
    <a href="upload.php"><button>Add Image</button></a>
    <hr>
    <table>
        <thead>
            <th>No. </th>
            <th>File Name</th>
            <th>File Url</th>
            <th>Preview</th>
            <th>Action</th>
        </thead>

        <tbody>
            <?php 
                do {
                    $i = 0;
                    foreach ($result->getBlobs() as $blob){
                        echo "<tr>";
                        echo "<td>".++$i."</td>";
                        echo "<td>".$blob->getName()."</td>";
                        echo "<td>".$blob->getUrl()."</td>";
                        echo '<td><img src=""></td>';
                        echo '<td>
                            <input type="hidden" name="imageUrl" id="imageUrl" value="'.$blob->getUrl().'">
                            <button onclick="proccessImage()">Analyze</button>
                            </td>';
                        echo "<tr>";
                    } $listblobs->setContinuationToken($result->getContinuationToken());
                } while ($result->getContinuationToken());
            ?>
        </tbody>
    </table>

    <hr>

    <!-- Form Show response from Computer Vision -->
    <h2>Show Respon From Analyze | Computer Vision</h2>
    <div class="col-md-12">
        <div id="wrapper" style="width:1020px; display:table;">
            <div id="jsonOutput" style="width:600px; display:table-cell;">
                Response:
                <br><br>
                <textarea id="responseTextArea" class="UIInput"
                style="width:580px; height:400px;"></textarea>
            </div>
            <div id="imageDiv" style="width:420px; display:table-cell;">
                Source image:
                <br><br>
                <img id="sourceImage" width="400" />
            </div>
        </div>
    </div>

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

        if (!$blobclient->containerExists($containername)){
            $createcontainer = new CreateContainerOptions();
            $createcontainer->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

            $createcontainer->addMetaData("key1", "value1");
            $createcontainer->addMetaData("key2", "value2");

            $blobclient->createContainer($containername, $createcontainer);
        }

        $listblobs = new ListBlobsOptions();
        $listblobs->setPrefix("");

        $result = $blobclient->listBlobs($containername, $listblobs);
    ?>

    <script type="text/javascript"> 
        function processImage(){
            var subscriptionKey = "2e2671970d6b469399ac05285a925f3e";
            var uriBase = "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";
            var params = {
                "visualFeatures": "Categories,Description,Color",
                "details": "",
                "language": "en",
            };
            // Display the image.
            var sourceImageUrl = document.getElementById("imageUrl").value;
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
</body>
</html>