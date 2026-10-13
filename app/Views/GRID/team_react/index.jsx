import React from 'react';
import { createRoot } from 'react-dom/client';
import TeamApp from './TeamApp';

const container = document.getElementById('grid-team-root');
if (container) {
    const root = createRoot(container);
    root.render(<TeamApp />);
}
