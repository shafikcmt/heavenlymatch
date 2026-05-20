import { defineConfig } from "prisma/config";

export default defineConfig({
  schema: "./prisma/schema.prisma",
  datasource: {
    // Prisma CLI requires mysql:// scheme; runtime adapter uses mariadb://
    url: process.env.DATABASE_URL?.replace("mariadb://", "mysql://"),
  },
});
