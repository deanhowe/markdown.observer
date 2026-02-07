#!/bin/bash
# Check for case sensitivity issues in Inertia pages

echo "Checking Inertia page case sensitivity..."

errors=0

# Check all .tsx files in pages directory
for file in resources/js/pages/**/*.tsx; do
    basename=$(basename "$file" .tsx)
    
    # Check if first letter is uppercase
    if [[ ! $basename =~ ^[A-Z] ]]; then
        echo "❌ ERROR: $file should start with uppercase (found: $basename)"
        errors=$((errors + 1))
    fi
done

if [ $errors -eq 0 ]; then
    echo "✅ All Inertia pages use correct case"
    exit 0
else
    echo "❌ Found $errors case sensitivity issues"
    exit 1
fi
