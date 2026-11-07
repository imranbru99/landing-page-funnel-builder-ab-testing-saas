# API

Base: `/api/v1`

## Auth

Create a Sanctum personal access token for a user and send:

`Authorization: Bearer {token}`

## Public endpoints

### Assign experiment variant
`POST /api/v1/public/experiments/{id}/assign`

Body:
```json
{ "visitor_key": "optional-stable-id" }
```

### Track experiment event
`POST /api/v1/public/experiments/{id}/events`

Body:
```json
{
  "event_name": "conversion",
  "visitor_key": "visitor-123",
  "variant_id": 1,
  "value": 1
}
```

## Authenticated endpoints

- `GET /pages`
- `POST /pages/{id}/publish`
- `GET /contacts`
- `GET /experiments/{id}/summary`

## Public form capture

`POST /forms/{slug}/submit` (web + CSRF-exempt for embeds)