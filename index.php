<?php
echo "<!DOCTYPE html>";
echo '<html lang="en">';
echo "<head>";
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<meta http-equiv="X-UA-Compatible" content="ie=edge">';
echo "<title>Final Submission | Azure Developer Academy</title>";
echo '<script src="jquery.min.js"></script>';
echo "</head>";
echo "<body>";
echo "<h2>Image List From Blob Storage</h2>";
echo '<a href="upload.php"><button>Add Image</button></a>';
echo "<hr>";
echo "<table>";
echo "<thead>";
echo "<th>No. </th>";
echo "<th>File Name</th>";
echo "<th>File Url</th>";
echo "<th>Preview</th>";
echo "<th>Action</th>";
echo "</thead>";

echo "<tbody>";
    require_once 'vendor/autoload.php';
            
    use MicrosoftAzure\Storage\Blob\BlobRestProxy;
    use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
    use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
    use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
    use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

    $connect = "DefaultEndpointsProtocol=https;AccountName=meirusfandiwev;AccountKey=vwhIwbU1kaFKEZMFWTd5ng21ux0PA8P8XRgUgo6atp8xbKPYFStk5vz+7/lTIG8SyZ/37LGfYqQxqbsX/EIwCQ==;EndpointSuffix=core.windows.net";
    $containername = "meirusfandi";
    $blobclient = BlobRestProxy::createBlobService($connect);

    try {
        if (!$blobclient->containerExists($containername)){
            $createcontainer = new CreateContainerOptions();
            $createcontainer->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

            $createcontainer->addMetaData("key1", "value1");
            $createcontainer->addMetaData("key2", "value2");

            $blobclient->createContainer($containername, $createcontainer);
        }
    } catch(ServiceException $e){
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }catch(InvalidArgumentTypeException $e){
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
    $listblobs = new ListBlobsOptions();
    $listblobs->setPrefix("");

    $result = $blobclient->listBlobs($containername, $listblobs);

                if (sizeof($result->getBlobs()) >0){
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
                            echo "</tr>";
                        } $listblobs->setContinuationToken($result->getContinuationToken());
                    } while ($result->getContinuationToken());
                } else {
                    echo "<tr>";
                    echo '<td colspan="5">No Data on Storage</td>';
                    echo "</tr>";
                }
                    
        echo "</tbody>";
    echo "</table>";

    echo "<hr>";

    //<!-- Form Show response from Computer Vision -->
    echo "<h2>Show Respon From Analyze | Computer Vision</h2>";
    echo '<div class="col-md-12">';
    echo '<div id="wrapper" style="width:1020px; display:table;">';
    echo '<div id="jsonOutput" style="width:600px; display:table-cell;">';
    echo "      Response:";
    echo "      <br><br>";
    echo '      <textarea id="responseTextArea" class="UIInput"';
    echo '      style="width:580px; height:400px;"></textarea>';
    echo "  </div>";
    echo '  <div id="imageDiv" style="width:420px; display:table-cell;">';
    echo "      Source image:";
    echo "      <br><br>";
    echo '      <img id="sourceImage" width="400" />';
    echo "  </div>";
    echo "</div>";
    echo "</div>";
    
    echo '
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
                data: {"url": + " + sourceImageUrl + "},
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
    </script>';
echo "</body>";
echo "</html>";

?>