#!/bin/bash
# Script to restart Apache after PHP configuration changes

echo "Restarting Apache to apply PHP configuration changes..."
sudo systemctl restart apache2

if [ $? -eq 0 ]; then
    echo "✓ Apache restarted successfully"
    echo "✓ New PHP limits are now active (post_max_size: 50M, upload_max_filesize: 50M)"
    echo ""
    echo "You can now test the gang sheet download:"
    echo "1. Open http://192.168.1.50:8000/gang-sheet-builder"
    echo "2. Upload images"
    echo "3. Click 'Download PNG'"
else
    echo "✗ Failed to restart Apache"
    exit 1
fi
