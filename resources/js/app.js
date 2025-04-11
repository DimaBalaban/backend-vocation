import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import Chatbot from './components/Chatbot';

const container = document.getElementById('app');
const root = createRoot(container);

root.render(
    <React.StrictMode>
        <Chatbot />
    </React.StrictMode>
);
