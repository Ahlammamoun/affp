import { NextResponse } from "next/server";

export async function POST(req: Request) {
  let body: any = null;
  try {
    body = await req.json();
  } catch {
    return NextResponse.json({ message: "Invalid JSON body" }, { status: 400 });
  }

  const { email, password } = body ?? {};
  if (!email || !password) {
    return NextResponse.json({ message: "email and password required" }, { status: 400 });
  }

  const API_BASE = process.env.API_BASE_URL;
  if (!API_BASE) {
    return NextResponse.json({ message: "API_BASE_URL missing" }, { status: 500 });
  }

  const r = await fetch(`${API_BASE}/register`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email, password }),
  });

  const txt = await r.text().catch(() => "");
  if (!r.ok) {
    // renvoie tel quel (json ou texte)
    return NextResponse.json({ message: "Register failed", detail: txt }, { status: r.status });
  }

  let data: any = {};
  try { data = JSON.parse(txt); } catch {}
  return NextResponse.json(data, { status: 201 });
}
