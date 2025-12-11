import React from 'react';
// IMPORTANTE: Importamos el componente vecino
import AreaCard from './AreaCard'; 

// --- DATOS SIMULADOS (MOCK DATA) ---
// Aquí más adelante conectarás tu Base de Datos real
const datosDelTaller = [
  {
    id: 1,
    nombre: "Barnizado",
    cola_espera: 5,
    operarios: [
      { nombre: "Juana", carga: 3 },
      { nombre: "Mary", carga: 5 } 
    ]
  },
  {
    id: 2,
    nombre: "Pintura",
    cola_espera: 10, // ALERTA: Mucha cola
    operarios: []    // ALERTA: Nadie trabajando -> TARJETA ROJA
  },
  {
    id: 3,
    nombre: "Tapicería",
    cola_espera: 1,
    operarios: [
      { nombre: "Pedro", carga: 2 } // TARJETA NARANJA (Poco trabajo)
    ]
  }
];

const Dashboard = () => {
  return (
    <div style={{ padding: '40px', backgroundColor: '#f0f2f5', minHeight: '100vh' }}>
      
      {/* Título Principal */}
      <div style={{ marginBottom: '30px', textAlign: 'center' }}>
        <h1 style={{ color: '#1a237e', margin: 0 }}>Panel Maestro de Producción</h1>
        <p style={{ color: '#5c6bc0', marginTop: '5px' }}>Vista general de flujo y alertas</p>
      </div>
      
      {/* Contenedor de Tarjetas (Grid Flex) */}
      <div style={{ 
        display: 'flex', 
        flexWrap: 'wrap', 
        justifyContent: 'center', 
        gap: '20px',
        alignItems: 'flex-start' 
      }}>
        {datosDelTaller.map((area) => (
          <AreaCard key={area.id} areaData={area} />
        ))}
      </div>

    </div>
  );
};

export default Dashboard;