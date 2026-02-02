(function () {
  const tbody = document.getElementById("productsTbody");
  if (!tbody) return;

  const LOW_STOCK = 10;
  const isAdmin = !!window.__IS_ADMIN__;

  function escapeHtml(str) {
    return String(str)
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  function formatPrice(p) {
    const n = Number(p);
    if (Number.isNaN(n)) return "Rs0.00";
    return "Rs" + n.toFixed(2);
  }

  // ✅ FIXED: EXACTLY 7 <td> TO MATCH TABLE HEADER
  function renderRows(items) {
    let html = "";

    for (const it of items) {
      const low = it.stock < LOW_STOCK;
      const status = low
        ? `<span class="badge low">Low Stock</span>`
        : `<span class="badge ok">OK</span>`;

      html += `
        <tr>
          <!-- ID -->
          <td>${it.product_id}</td>

          <!-- Product -->
          <td>${escapeHtml(it.product_name)}</td>

          <!-- Supplier -->
          <td>${escapeHtml(it.supplier_name)}</td>

          <!-- Price -->
          <td>${formatPrice(it.price)}</td>

          <!-- Stock -->
          <td>
            ${
              isAdmin
                ? `<input
                      type="number"
                      class="stock-input"
                      value="${it.stock}"
                      data-id="${it.product_id}"
                      min="0"
                  >`
                : it.stock
            }
          </td>

          <!-- Status -->
          <td>${status}</td>

          <!-- Actions -->
          <td class="actions">
            ${
              isAdmin
                ? `
                  <a class="btn" href="edit.php?id=${it.product_id}">Edit</a>
                  <a class="btn danger" href="delete.php?id=${it.product_id}">Delete</a>
                `
                : ""
            }
          </td>
        </tr>
      `;
    }

    tbody.innerHTML = html;
  }

  async function refreshStock() {
    try {
      const res = await fetch("ajax_stock.php", { cache: "no-store" });
      const data = await res.json();
      if (!data.ok) return;
      renderRows(data.items || []);
    } catch (e) {
      console.error(e);
    }
  }

  // Load immediately + refresh every 3 seconds
  refreshStock();
  setInterval(refreshStock, 3000);
})();

// ✅ Stock update handler
document.addEventListener("change", async function (e) {
  if (!e.target.classList.contains("stock-input")) return;

  const productId = e.target.dataset.id;
  const newStock = e.target.value;

  try {
    await fetch("update_stock.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `id=${productId}&stock=${newStock}`,
    });
  } catch (err) {
    alert("Failed to update stock");
  }
});
