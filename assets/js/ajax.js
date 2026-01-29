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
    // simple currency formatting (₹)
    const n = Number(p);
    if (Number.isNaN(n)) return "₹0.00";
    return "₹" + n.toFixed(2);
  }

  function renderRows(items) {
    let html = "";

    for (const it of items) {
      const low = it.stock < LOW_STOCK;
      const status = low
        ? `<span class="badge low">Low Stock</span>`
        : `<span class="badge ok">OK</span>`;

      const actions = isAdmin
        ? `<td class="actions">
             <a class="btn" href="edit.php?id=${it.product_id}">Edit</a>
             <a class="btn danger" href="delete.php?id=${it.product_id}">Delete</a>
           </td>`
        : "";

      html += `
        <tr>
          <td>${it.product_id}</td>
          <td>${escapeHtml(it.product_name)}</td>
          <td>${escapeHtml(it.supplier_name)}</td>
          <td>${formatPrice(it.price)}</td>
          <td>${it.stock}</td>
          <td>${status}</td>
          ${actions}
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
      // ignore
    }
  }

  // Load immediately, then every 3 seconds
  refreshStock();
  setInterval(refreshStock, 3000);
})();
