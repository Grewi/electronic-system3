CREATE TABLE "history" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "uri" text NOT NULL,
  "method" text NOT NULL,
  "tab" real NOT NULL,
  "ajax" integer NOT NULL,
  "session" text NOT NULL,
  "datetime" integer NOT NULL,
  "status" integer NOT NULL
)