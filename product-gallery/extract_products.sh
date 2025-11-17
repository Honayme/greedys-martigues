#!/bin/bash

cd "/c/laragon/www/greedys-martigues/product-gallery"

# Process all HTML files (excluding GALERIE)
find . -maxdepth 1 -name "*.html" -type f ! -name "GALERIE*" | sort | while read html; do
  # Extract title from HTML
  title=$(grep -o '<h1 class="page-title">[^<]*' "$html" | sed 's/<h1 class="page-title">//')

  if [ -n "$title" ]; then
    # Extract image filenames
    images=$(grep -o 'src="[^"]*\.jpg"' "$html" | sed 's/src="//;s/"//' | tr '\n' '|')
    images=${images%|}  # Remove trailing |

    echo "PRODUCT: $title"
    echo "HTML: $(basename "$html")"
    echo "IMAGES: $images"
    echo "---"
  fi
done
