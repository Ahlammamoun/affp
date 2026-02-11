import { NextResponse } from "next/server";

export async function POST(req: Request) {
  try {
    const body = await req.json();
    const API_BASE = process.env.API_BASE_URL;

    if (!API_BASE) {
      return NextResponse.json(
        { message: "API_BASE_URL is missing in frontend env" },
        { status: 500 }
      );
    }

    const url = `${API_BASE}/login_check`;
    const r = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(body),
    });

    const raw = await r.text();

    if (!r.ok) {
      return NextResponse.json(
        { message: "Bad credentials or backend error", detail: raw },
        { status: r.status === 401 ? 401 : 502 }
      );
    }

    let data: any = {};
    try {
      data = raw ? JSON.parse(raw) : {};
    } catch {
      return NextResponse.json(
        { message: "Backend did not return JSON", detail: raw },
        { status: 502 }
      );
    }

    const token = data.token ?? data.access_token ?? data.jwt;
    if (!token) {
      return NextResponse.json(
        { message: "Token not found in backend response", data },
        { status: 500 }
      );
    }

    const res = NextResponse.json({ ok: true });
    res.cookies.set("auth_token", token, {
      httpOnly: true,
      secure: process.env.NODE_ENV === "production",
      sameSite: "lax",
      path: "/",
    });

    return res;
  } catch (e: any) {
    return NextResponse.json(
      { message: "Login route crashed", detail: String(e?.message ?? e) },
      { status: 500 }
    );
  }
}
