/**
 * messagingState.js
 * State contract for GRID Messaging Workspace (Circle Connect).
 * Hardcoded data completely removed. Dynamic database-backed communication only.
 */

export const filterTabs = [
    { id: 'focused', label: 'Focused', hasArrow: true },
    { id: 'jobs', label: 'Jobs', hasArrow: false },
    { id: 'unread', label: 'Unread', hasArrow: false },
    { id: 'connections', label: 'Connections', hasArrow: false },
    { id: 'efactor', label: 'eFactor', hasArrow: false },
    { id: 'starred', label: 'Starred', hasArrow: false }
];

export const initialConversations = [];
