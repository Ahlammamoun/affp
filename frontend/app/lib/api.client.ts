const BROWSER_API = "/api";

export async function apiGetClient<T>(path: string): Promise<T> {
  const res = await fetch(`${BROWSER_API}${path}`, {
    cache: "no-store",
    credentials: "include",
  });

  if (!res.ok) throw new Error(await res.text().catch(() => ""));
  return res.json() as Promise<T>;
}
