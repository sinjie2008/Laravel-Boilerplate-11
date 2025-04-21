<?php
        
        echo "Starting Zip Test...<br>";
        
        $zipFileName = __DIR__ . '/test_archive.zip';
        $testFileName = __DIR__ . '/test_file.txt';
        $testFileContent = 'This is a test file for ZipArchive.';
        
        // Create a dummy file to zip
        if (file_put_contents($testFileName, $testFileContent) === false) {
            echo "Error: Could not create test file: " . $testFileName . "<br>";
            exit;
        } else {
            echo "Successfully created test file: " . $testFileName . "<br>";
        }
        
        $zip = new ZipArchive();
        
        // Try to open/create the zip file
        $res = $zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($res !== true) {
            echo "Error: Could not open/create zip file '{$zipFileName}'. Code: " . $res . "<br>";
            @unlink($testFileName); // Clean up test file
            exit;
        } else {
            echo "Successfully opened/created zip file: " . $zipFileName . "<br>";
        }
        
        // Try to add the test file
        if (!$zip->addFile($testFileName, 'inside_zip_test.txt')) {
            echo "Error: Could not add file '{$testFileName}' to zip.<br>";
        } else {
            echo "Successfully added file to zip.<br>";
        }
        
        // Close the zip file (this writes it to disk)
        if (!$zip->close()) {
            echo "Error: Could not close (save) the zip file.<br>";
        } else {
            echo "Successfully closed (saved) the zip file.<br>";
        }
        
        // Clean up the dummy file
        @unlink($testFileName);
        
        // Check if the final zip exists and has content
        if (file_exists($zipFileName) && filesize($zipFileName) > 0) {
            echo "SUCCESS: Zip file '{$zipFileName}' created and seems valid.<br>";
            // Optionally delete the test zip: @unlink($zipFileName);
        } else {
            echo "FAILURE: Zip file '{$zipFileName}' was not created correctly or is empty.<br>";
        }
        
        echo "Zip Test Finished.<br>";
        
        ?>
