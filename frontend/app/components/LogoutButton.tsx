"use client";

import { useRouter } from "next/navigation";

export default function LogoutButton() {
  const router = useRouter();

  return (
    <button
      type="button"
      className="btn"
      onClick={async () => {
        try {
          const r = await fetch("/api/logout", {
            method: "POST",
            credentials: "include",
            headers: { Accept: "application/json" },
          });

          // même si 401/404, on force la sortie côté UI
          // mais on log pour debug
          if (!r.ok) console.warn("Logout failed:", r.status, await r.text().catch(() => ""));
        } finally {
          router.replace("/");
          router.refresh();
        }
      }}
    >
      Se déconnecter
    </button>
  );
}
