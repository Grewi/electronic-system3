CREATE TABLE "history" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "hash" text NOT NULL,
  "uri" text NOT NULL,
  "method" text NOT NULL,
  "tab" real NULL,
  "session" text NOT NULL,
  "datetime" integer NOT NULL,
  "status" integer NOT NULL
)