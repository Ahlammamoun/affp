import { NextResponse } from "next/server";
import { cookies } from "next/headers";

export async function POST() {
  const token = (await cookies()).get("auth_token")?.value;
  if (!token) {
    return NextResponse.json({ message: "Not authenticated" }, { status: 401 });
  }

  const API_BASE = process.env.API_BASE_URL; // ex: http://nginx/api
  if (!API_BASE) {
    return NextResponse.json({ message: "API_BASE_URL missing" }, { status: 500 });
  }

  const r = await fetch(`${API_BASE}/billing/checkout`, {
    method: "POST",
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });

  const txt = await r.text().catch(() => "");
  if (!r.ok) {
    return NextResponse.json({ message: "Checkout failed", detail: txt }, { status: r.status });
  }

  // backend renvoie { url }
  let data: any = {};
  try { data = JSON.parse(txt); } catch {}
  return NextResponse.json(data);
}
