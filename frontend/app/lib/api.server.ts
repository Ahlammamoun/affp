import "server-only";
import { cookies } from "next/headers";

const SERVER_API = "http://nginx/api";

export async function apiGetServer<T>(path: string): Promise<T> {
  const store = await cookies();
  const token = store.get("auth_token")?.value;

  const res = await fetch(`${SERVER_API}${path}`, {
    cache: "no-store",
    headers: token ? { Authorization: `Bearer ${token}` } : {},
    credentials: "include",
  });

  if (!res.ok) throw new Error(await res.text().catch(() => ""));
  return res.json() as Promise<T>;
}
