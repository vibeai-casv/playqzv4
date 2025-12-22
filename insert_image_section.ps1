# Read the file content
 = 'e:\projects\playqzv4\client\src\pages\admin\BulkEditQuestions.tsx'
 = Get-Content 'e:\projects\playqzv4\temp_image_section.tsx' -Raw
 = Get-Content  -Raw

# Find the location to insert (after options section, before explanation)
# We'll insert after line containing ')}\r\n\r\n' that's before '{question.explanation'
 = '(?<=\)\}\r\n\r\n)(?=\s+\{question\.explanation)'

# Insert the image section
 =  -replace , "

                                    "

# Write back
 | Set-Content  -NoNewline

Write-Host "Image display section added successfully!"
