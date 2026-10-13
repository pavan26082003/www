<?php
session_start();

if (isset($_SESSION['fileUrls'])) { 
    $fileUrls = $_SESSION['fileUrls']; 
    $mediaFiles = []; 
    foreach ($fileUrls as $fileUrl) { 
        $fileExtension = pathinfo($fileUrl, PATHINFO_EXTENSION); 
        if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) { 
            $mediaFiles[] = ['type' => 'image', 'url' => $fileUrl];
        } elseif (in_array(strtolower($fileExtension), ['mp4', 'avi', 'mov', 'webm'])) { 
            $mediaFiles[] = ['type' => 'video', 'url' => $fileUrl];
        } else { 
            $mediaFiles[] = ['type' => 'unsupported', 'url' => $fileUrl];
        }
    }

    // Add responsive styles for desktop and mobile compatibility
    echo '<style>
            .media-container {
                width: 100%;
                max-width: 500px; /* Smaller size for desktop */
                margin-bottom: 30px;
            }
            .media-container img,
            .media-container video {
                width: 100%;
                height: auto;
            }
            
            /* Adjustments for larger screens */
            @media (min-width: 1024px) {
                .media-container {
                    max-width: 400px; /* Further reduce size on desktop */
                }
            }
          </style>';

    echo '<div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-top: 20px;">'; 
    foreach ($mediaFiles as $mediaFile) {
        echo '<div class="media-container">';   
        if ($mediaFile['type'] === 'image') { 
            echo '<img src="' . $mediaFile['url'] . '" alt="Image" />';
        } elseif ($mediaFile['type'] === 'video') { 
            echo '<video controls>
                    <source src="' . $mediaFile['url'] . '" type="video/' . pathinfo($mediaFile['url'], PATHINFO_EXTENSION) . '">
                    Your browser does not support the video tag.
                  </video>';
        } else { 
            echo 'Unsupported file: ' . $mediaFile['url'];
        } 
        echo '</div>';
    } 
    unset($_SESSION['fileUrls']);
} else {
    echo "No files available.";
}
?>
