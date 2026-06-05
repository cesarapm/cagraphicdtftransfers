#!/usr/bin/env python3
"""
Background Removal Script using rembg
Usage: python remove_background.py <input_image> <output_image>

Requirements:
    pip install rembg pillow
"""

import sys
from rembg import remove
from PIL import Image

def remove_background(input_path, output_path):
    """
    Remove background from an image and save as PNG with transparency
    
    Args:
        input_path: Path to input image
        output_path: Path to save output image
    """
    try:
        # Open the input image
        input_image = Image.open(input_path)
        
        # Remove the background
        output_image = remove(input_image)
        
        # Save as PNG with transparency
        output_image.save(output_path, 'PNG')
        
        print(f"Successfully removed background: {output_path}")
        return 0
        
    except Exception as e:
        print(f"Error: {str(e)}", file=sys.stderr)
        return 1

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python remove_background.py <input_image> <output_image>")
        sys.exit(1)
    
    input_path = sys.argv[1]
    output_path = sys.argv[2]
    
    exit_code = remove_background(input_path, output_path)
    sys.exit(exit_code)
