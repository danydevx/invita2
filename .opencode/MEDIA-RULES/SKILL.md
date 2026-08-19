# Skill: Media Upload Rules

Use Laravel validation for all uploads.

Gallery:
- max 20 images per business
- only jpg/jpeg
- max 2048 KB
- store in storage/app/public/businesses/{business_id}/gallery

Product:
- main image optional
- max 5 extra images
- only jpg/jpeg
- max 2048 KB
- store in storage/app/public/businesses/{business_id}/products

Service:
- image optional
- only jpg/jpeg
- max 2048 KB
- store in storage/app/public/businesses/{business_id}/services

Never allow members to overwrite another member's files.

Use Storage facade.

Create image deletion logic when records are deleted.

Do not use base64 images.
