import { NextResponse } from "next/server";

const API_BASE = process.env.API_BASE_URL ?? "http://nginx/api";

export async function POST(req: Request) {
  // on forward le JWT (cookie) vers Symfony
  const cookie = req.headers.get("cookie") ?? "";

  const r = await fetch(`${API_BASE}/refresh`, {
    method: "POST",
    headers: { cookie },
    cache: "no-store",
  });

  if (!r.ok) {
    const txt = await r.text().catch(() => "");
    return NextResponse.json({ message: txt || "Refresh failed" }, { status: r.status });
  }

  const data = await r.json().catch(() => null);
  const token = data?.token;

  if (!token) {
    return NextResponse.json({ message: "Missing token" }, { status: 500 });
  }

  const res = NextResponse.json({ ok: true });
  res.cookies.set("auth_token", token, {
    httpOnly: true,
    sameSite: "lax",
    path: "/",
    // secure: true, // active en prod HTTPS
  });

  return res;
}
