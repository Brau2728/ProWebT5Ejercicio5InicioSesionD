import React from 'react';
// Importaciones de Material UI (MUI)
import { Card, Avatar, Tooltip, Badge, Chip, Typography, Box } from '@mui/material';
import WarningAmberRoundedIcon from '@mui/icons-material/WarningAmberRounded';
import CheckCircleOutlineRoundedIcon from '@mui/icons-material/CheckCircleOutlineRounded';

const AreaCard = ({ areaData }) => {
  // Desestructuración de datos
  const { nombre, cola_espera, operarios } = areaData;

  // --- LÓGICA DE NEGOCIO Y VISUAL ---
  // 1. Calcular carga total actual
  const totalActivos = operarios.reduce((acc, curr) => acc + curr.carga, 0);
  
  // 2. Definir estados y colores (Semáforo)
  let estadoColor = '#4caf50'; // Verde (Óptimo)
  let mensajeAlerta = null;

  // Regla Crítica: Cuello de Botella
  if (cola_espera > 5 && totalActivos === 0) {
    estadoColor = '#d32f2f'; // Rojo (Peligro)
    mensajeAlerta = "¡Línea detenida con cola!";
  } 
  // Regla Advertencia: Bajo flujo
  else if (totalActivos < 3 && cola_espera < 2) {
    estadoColor = '#ed6c02'; // Naranja (Warning)
    mensajeAlerta = "Bajo flujo de trabajo";
  }

  return (
    <Card 
      sx={{ 
        width: 300, // Ancho fijo para uniformidad
        m: 2, 
        p: 2, 
        borderRadius: 4,
        borderTop: `6px solid ${estadoColor}`, 
        boxShadow: '0 8px 24px rgba(0,0,0,0.12)', // Sombra suave moderna
        transition: 'transform 0.2s', // Efecto hover
        '&:hover': { transform: 'translateY(-4px)' }
      }}
    >
      {/* ENCABEZADO */}
      <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
        <Typography variant="h6" fontWeight="bold" color="text.primary">
          {nombre}
        </Typography>
        {mensajeAlerta ? (
          <Tooltip title={mensajeAlerta}>
            <WarningAmberRoundedIcon sx={{ color: estadoColor, fontSize: 28 }} />
          </Tooltip>
        ) : (
          <CheckCircleOutlineRoundedIcon sx={{ color: '#e0e0e0', fontSize: 28 }} />
        )}
      </Box>

      {/* METRICAS (GRID) */}
      <Box display="flex" justifyContent="space-between" mb={3} sx={{ backgroundColor: '#f9fafb', borderRadius: 2, p: 1.5 }}>
        <Box textAlign="center" flex={1} borderRight="1px solid #eee">
          <Typography variant="caption" color="text.secondary" fontWeight="bold">EN COLA</Typography>
          <Typography variant="h4" fontWeight="800" color="text.secondary">{cola_espera}</Typography>
        </Box>
        <Box textAlign="center" flex={1}>
          <Typography variant="caption" sx={{ color: estadoColor, fontWeight: 'bold' }}>EN PROCESO</Typography>
          <Typography variant="h4" fontWeight="800" sx={{ color: estadoColor }}>{totalActivos}</Typography>
        </Box>
      </Box>

      {/* OPERARIOS */}
      <Box>
        <Typography variant="caption" fontWeight="bold" color="text.disabled" mb={1} display="block">
          PERSONAL ASIGNADO
        </Typography>
        <Box display="flex" gap={1} flexWrap="wrap">
          {operarios.length > 0 ? (
            operarios.map((op, index) => (
              <Tooltip key={index} title={`${op.nombre}: ${op.carga} muebles`}>
                <Badge badgeContent={op.carga} color={op.carga > 4 ? "error" : "primary"}>
                  <Avatar 
                    sx={{ width: 36, height: 36, bgcolor: op.carga > 4 ? '#1565c0' : '#90caf9', fontSize: '0.9rem' }}
                  >
                    {op.nombre.charAt(0)}
                  </Avatar>
                </Badge>
              </Tooltip>
            ))
          ) : (
             <Chip label="Sin personal" size="small" sx={{opacity: 0.7}} />
          )}
        </Box>
      </Box>

      {/* FOOTER DE ALERTA */}
      {mensajeAlerta && (
        <Box mt={2} p={1} bgcolor={estadoColor + '15'} borderRadius={1} display="flex" alignItems="center">
          <Typography variant="caption" color={estadoColor} fontWeight="bold">
            ⚠️ {mensajeAlerta}
          </Typography>
        </Box>
      )}
    </Card>
  );
};

export default AreaCard;