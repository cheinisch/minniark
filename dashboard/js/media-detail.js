/* media-detail.js — robust modal controller (handles duplicate IDs) */
(() => {
  console.log("[media-detail] modal init (robust)");

  const lockScroll = () => {
    document.documentElement.classList.add("overflow-hidden");
    document.body.classList.add("overflow-hidden");
  };
  const unlockScroll = () => {
    document.documentElement.classList.remove("overflow-hidden");
    document.body.classList.remove("overflow-hidden");
  };

  // If IDs are duplicated, querySelectorAll will return all; we take the LAST one.
  const getLastById = (id) => {
    const list = document.querySelectorAll(`#${CSS.escape(id)}`);
    return list.length ? list[list.length - 1] : null;
  };

  const isOpen = (modal) => modal && !modal.classList.contains("hidden");

  const openModal = (modal, focusSelector) => {
    if (!modal) return;
    modal.classList.remove("hidden");
    modal.setAttribute("aria-hidden", "false");
    lockScroll();

    const focusEl =
      (focusSelector && modal.querySelector(focusSelector)) ||
      modal.querySelector("button, [href], input, textarea, select, [tabindex]:not([tabindex='-1'])");

    if (focusEl?.focus) setTimeout(() => focusEl.focus(), 0);
  };

  const closeModal = (modal, returnFocusEl) => {
    if (!modal) return;
    modal.classList.add("hidden");
    modal.setAttribute("aria-hidden", "true");
    unlockScroll();
    if (returnFocusEl?.focus) setTimeout(() => returnFocusEl.focus(), 0);
  };

  const findBackdrop = (modal) => {
    if (!modal) return null;
    // your backdrop is usually a child with fixed inset-0 AND a bg- class
    const candidates = modal.querySelectorAll(":scope > .fixed.inset-0, .fixed.inset-0");
    for (const el of candidates) {
      const cls = (el.className || "").toString();
      if (el !== modal && cls.includes("bg-")) return el;
    }
    return null;
  };

  const findPanel = (modal) => {
    if (!modal) return null;
    // panel is usually the inner "card"
    return (
      modal.querySelector(".relative.transform.overflow-hidden") ||
      modal.querySelector(".relative.transform") ||
      modal.querySelector("[data-panel]") ||
      null
    );
  };

  function wireModal({
    modalId,
    openBtnId,
    closeSelectors,     // selectors INSIDE the modal (no global getElementById!)
    focusSelector,
  }) {
    const modal = getLastById(modalId);
    const openBtn = document.getElementById(openBtnId);

    if (!modal || !openBtn) {
      console.warn("[media-detail] wireModal missing:", { modalId, openBtnId, modal: !!modal, openBtn: !!openBtn });
      return null;
    }

    // Avoid double binding
    if (modal.dataset.bound === "true") return { modal, openBtn };
    modal.dataset.bound = "true";

    const backdrop = findBackdrop(modal);
    const panel = findPanel(modal);

    // OPEN
    openBtn.addEventListener("click", (e) => {
      e.preventDefault();
      openModal(modal, focusSelector);
    });

    // CLOSE buttons (X, Cancel, etc.) — query INSIDE the modal instance
    closeSelectors.forEach((sel) => {
      const btn = modal.querySelector(sel);
      if (!btn) return;
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        closeModal(modal, openBtn);
      });
    });

    // Backdrop click closes
    if (backdrop) {
      backdrop.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        closeModal(modal, openBtn);
      });
    }

    // Click outside panel closes (safe fallback)
    modal.addEventListener("click", (e) => {
      if (panel && panel.contains(e.target)) return;
      // allow clicking overlay area to close
      if (e.target === modal) closeModal(modal, openBtn);
    });

    return { modal, openBtn };
  }

  // --- AI modal ---
  const ai = wireModal({
    modalId: "confirmAiModal",
    openBtnId: "generate_text",
    closeSelectors: ["#aiTextClose", "#confirmNo"],
    focusSelector: "#confirmNo",
  });

  // --- Edit Text modal ---
  const edit = wireModal({
    modalId: "editImageTextModal",
    openBtnId: "edit_text",
    closeSelectors: ["#close_edit_image_text", "#cancel_image_text"],
    focusSelector: "#image-title-input",
  });

  // ESC closes topmost open (edit first, then ai)
  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") return;

    if (edit?.modal && isOpen(edit.modal)) {
      e.preventDefault();
      closeModal(edit.modal, edit.openBtn);
      return;
    }
    if (ai?.modal && isOpen(ai.modal)) {
      e.preventDefault();
      closeModal(ai.modal, ai.openBtn);
      return;
    }
  });

  console.log("[media-detail] modal ready", {
    editModalFound: !!edit?.modal,
    aiModalFound: !!ai?.modal,
  });
})();
