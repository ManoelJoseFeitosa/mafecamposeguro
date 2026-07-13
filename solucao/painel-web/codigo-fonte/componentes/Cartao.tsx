interface PropriedadesCartao {
  titulo: string;
  valor: string;
}

export default function Cartao({ titulo, valor }: PropriedadesCartao) {
  return (
    <div className="cartao-indicador">
      <div className="cartao-indicador__titulo">{titulo}</div>
      <div className="cartao-indicador__valor">{valor}</div>
    </div>
  );
}
