// app/lib/api.ts
import { cookies } from "next/headers";

const SERVER_API = process.env.SERVER_API || "http://nginx/api";
const BROWSER_API = "/api";

function baseUrl() {
  return typeof window === "undefined" ? SERVER_API : BROWSER_API;
}

function joinUrl(base: string, path: string) {
  const b = base.endsWith("/") ? base.slice(0, -1) : base;
  const p = path.startsWith("/") ? path : `/${path}`;
  return `${b}${p}`;
}

export async function apiGet<T>(path: string, timeoutMs = 3000): Promise<T> {
  const url = joinUrl(baseUrl(), path);

  // cookies() est async en SSR, mais ne bloque pas longtemps
  if (typeof window === "undefined") {
    await cookies();
  }

  const ctrl = new AbortController();
  const t = setTimeout(() => ctrl.abort(), timeoutMs);

  try {
    const res = await fetch(url, {
      cache: "no-store",
      headers: { Accept: "application/json" },
      signal: ctrl.signal,
    });

    if (!res.ok) {
      const txt = await res.text().catch(() => "");
      throw new Error(`API error ${res.status}: ${txt}`);
    }
    return (await res.json()) as T;
  } finally {
    clearTimeout(t);
  }
}
