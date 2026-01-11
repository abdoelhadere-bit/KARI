console.log("✅ app.js chargé");

document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("acctBtn");
  const menu = document.getElementById("acctMenu");
  if (!btn || !menu) return;

  const isOpen = () => menu.classList.contains("open");

  const setOpen = (open) => {
    menu.classList.toggle("open", open);
    menu.setAttribute("aria-hidden", open ? "false" : "true");
    btn.setAttribute("aria-expanded", open ? "true" : "false");

    // ✅ sécurité: marche même si tu utilises du CSS basé sur [hidden]
    menu.hidden = !open;
  };

  // ✅ état initial (fermé)
  setOpen(false);

  // toggle button
  btn.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();
    setOpen(!isOpen());
  });

  // click dans menu => ne ferme pas
  menu.addEventListener("click", (e) => e.stopPropagation());

  // click dehors => ferme
  document.addEventListener("click", (e) => {
    if (!isOpen()) return;
    if (btn.contains(e.target) || menu.contains(e.target)) return;
    setOpen(false);
  });

  // ESC => ferme
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") setOpen(false);
  });
});

// ===== Confirm modal =====
document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("confirmModal");
  if (!modal) return;

  const titleEl = document.getElementById("cmTitle");
  const msgEl   = document.getElementById("cmMsg");
  const goEl    = document.getElementById("cmGo");

  const open = ({ title, message, href }) => {
    titleEl.textContent = title || "Confirmation";
    msgEl.textContent   = message || "Tu es sûr ?";
    goEl.setAttribute("href", href || "#");

    modal.classList.add("show");
    modal.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
  };

  const close = () => {
    modal.classList.remove("show");
    modal.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
  };

document.addEventListener("click", (e) => {
    const a = e.target.closest("a.js-confirm");
    if (!a) return;

    e.preventDefault();

    open({
      title: a.dataset.title || "Confirmation",
      message: a.dataset.message || "Confirmer cette action ?",
      href: a.getAttribute("href"),
    });
  });

  // Close modal
  modal.addEventListener("click", (e) => {
    if (e.target.dataset.close === "1") close();
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") close();
  });
});

