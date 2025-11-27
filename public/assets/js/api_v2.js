// public/assets/js/api.js

// FIX: This MUST be an empty string since your site is now hosted at the root domain.
const basePath = ''; 

export async function fetchData(endpoint, params = {}) {
    try {
        // This will now correctly build the URL as: https://yourdomain.com/residents/process...
        const url = new URL(`${basePath}/${endpoint}`, window.location.origin);
        Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));
        
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error(`Failed to fetch from ${endpoint}:`, error);
        return { error: `Could not load data from ${endpoint}.` };
    }
}