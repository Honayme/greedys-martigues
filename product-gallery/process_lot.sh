#!/bin/bash

cd /c/laragon/www/greedys-martigues/product-gallery

# Process first 7 HTML files (excluding GALERIE and CSV)
count=0
find . -maxdepth 1 -name "*.html" -type f ! -name "GALERIE*" ! -name "*CSV*" | sort | while read html && [ $count -lt 7 ]; do
  count=$((count+1))

  # Extract title from HTML
  title=$(sed -n 's/.*<h1 class="page-title">\(.*\)<\/h1>.*/\1/p' "$html")

  if [ -n "$title" ]; then
    # Extract image filenames
    sed -n 's/.*<img style="width:480px" src="\([^"]*\.jpg\)".*/\1/p' "$html" > /tmp/images.txt

    echo "=== PRODUCT ==="
    echo "FILE: $html"
    echo "TITLE: $title"
    echo "IMAGES:"
    cat /tmp/images.txt
    echo ""
  fi
done
