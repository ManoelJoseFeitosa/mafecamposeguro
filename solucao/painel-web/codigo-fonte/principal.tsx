import React from "react";
import ReactDOM from "react-dom/client";
import Aplicativo from "./Aplicativo";
import "./estilos.css";

ReactDOM.createRoot(document.getElementById("raiz")!).render(
  <React.StrictMode>
    <Aplicativo />
  </React.StrictMode>,
);
