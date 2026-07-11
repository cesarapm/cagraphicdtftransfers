/**
 * Cart Storage Service
 * Maneja almacenamiento híbrido: localStorage para datos + IndexedDB para imágenes
 */

const DB_NAME = 'dtf_cart_db';
const DB_VERSION = 1;
const STORE_NAME = 'cart_images';

let db = null;

/**
 * Inicializar IndexedDB
 */
const initDB = () => {
    return new Promise((resolve, reject) => {
        if (db) {
            resolve(db);
            return;
        }

        const request = indexedDB.open(DB_NAME, DB_VERSION);

        request.onerror = () => {
            console.error('IndexedDB error:', request.error);
            reject(request.error);
        };

        request.onsuccess = () => {
            db = request.result;
            resolve(db);
        };

        request.onupgradeneeded = (event) => {
            const database = event.target.result;
            if (!database.objectStoreNames.contains(STORE_NAME)) {
                database.createObjectStore(STORE_NAME, { keyPath: 'itemId' });
            }
        };
    });
};

/**
 * Guardar imagen en IndexedDB
 * @param {string} itemId - ID único del item
 * @param {File|Blob|string} imageData - Archivo, Blob o Base64 string
 */
export const saveImageToIndexedDB = async (itemId, imageData) => {
    try {
        await initDB();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORE_NAME], 'readwrite');
            const store = transaction.objectStore(STORE_NAME);

            // Convertir Base64 a Blob si es necesario
            let dataToStore = imageData;
            if (typeof imageData === 'string' && imageData.startsWith('data:')) {
                dataToStore = base64ToBlob(imageData);
            } else if (imageData instanceof File) {
                dataToStore = new Blob([imageData], { type: imageData.type });
            }

            const request = store.put({
                itemId,
                imageData: dataToStore,
                timestamp: Date.now()
            });

            request.onerror = () => {
                console.error('Error saving image to IndexedDB:', request.error);
                reject(request.error);
            };

            request.onsuccess = () => {
                // console.log(`✅ Image saved for item ${itemId}`);
                resolve();
            };
        });
    } catch (error) {
        console.error('Error in saveImageToIndexedDB:', error);
        throw error;
    }
};

/**
 * Recuperar imagen desde IndexedDB
 * @param {string} itemId - ID único del item
 * @returns {Promise<string>} Base64 string o null
 */
export const getImageFromIndexedDB = async (itemId) => {
    try {
        await initDB();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORE_NAME], 'readonly');
            const store = transaction.objectStore(STORE_NAME);
            const request = store.get(itemId);

            request.onerror = () => {
                console.error('Error reading image from IndexedDB:', request.error);
                reject(request.error);
            };

            request.onsuccess = () => {
                if (request.result && request.result.imageData) {
                    blobToBase64(request.result.imageData)
                        .then(base64 => resolve(base64))
                        .catch(() => resolve(null));
                } else {
                    resolve(null);
                }
            };
        });
    } catch (error) {
        console.error('Error in getImageFromIndexedDB:', error);
        return null;
    }
};

/**
 * Eliminar imagen desde IndexedDB
 * @param {string} itemId - ID único del item
 */
export const deleteImageFromIndexedDB = async (itemId) => {
    try {
        await initDB();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORE_NAME], 'readwrite');
            const store = transaction.objectStore(STORE_NAME);
            const request = store.delete(itemId);

            request.onerror = () => {
                console.error('Error deleting image from IndexedDB:', request.error);
                reject(request.error);
            };

            request.onsuccess = () => {
                // console.log(`✅ Image deleted for item ${itemId}`);
                resolve();
            };
        });
    } catch (error) {
        console.error('Error in deleteImageFromIndexedDB:', error);
        throw error;
    }
};

/**
 * Limpiar todas las imágenes del carrito
 */
export const clearAllCartImages = async () => {
    try {
        await initDB();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORE_NAME], 'readwrite');
            const store = transaction.objectStore(STORE_NAME);
            const request = store.clear();

            request.onerror = () => reject(request.error);
            request.onsuccess = () => {
                // console.log('✅ All cart images cleared');
                resolve();
            };
        });
    } catch (error) {
        console.error('Error in clearAllCartImages:', error);
        throw error;
    }
};

/**
 * Convertir Base64 a Blob
 */
const base64ToBlob = (base64) => {
    const parts = base64.split(',');
    const mime = parts[0].match(/:(.*?);/)[1];
    const bstr = atob(parts[1]);
    const n = bstr.length;
    const u8arr = new Uint8Array(n);
    for (let i = 0; i < n; i++) {
        u8arr[i] = bstr.charCodeAt(i);
    }
    return new Blob([u8arr], { type: mime });
};

/**
 * Convertir Blob a Base64
 */
const blobToBase64 = (blob) => {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(blob);
    });
};

/**
 * Obtener tamaño total de imágenes en IndexedDB
 */
export const getIndexedDBStorageSize = async () => {
    try {
        await initDB();

        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORE_NAME], 'readonly');
            const store = transaction.objectStore(STORE_NAME);
            const request = store.getAll();

            request.onerror = () => reject(request.error);
            request.onsuccess = () => {
                let totalSize = 0;
                request.result.forEach(record => {
                    if (record.imageData instanceof Blob) {
                        totalSize += record.imageData.size;
                    }
                });
                resolve(totalSize);
            };
        });
    } catch (error) {
        console.error('Error getting IndexedDB size:', error);
        return 0;
    }
};

/**
 * Validar tamaño de imagen
 * @param {File|Blob} file - Archivo a validar
 * @param {number} maxSizeMB - Tamaño máximo en MB (default 5)
 */
export const validateImageSize = (file, maxSizeMB = 5) => {
    const maxBytes = maxSizeMB * 1024 * 1024;
    return file.size <= maxBytes;
};

/**
 * Comprimir imagen (redimensionar)
 * @param {File} file - Archivo a comprimir
 * @param {number} maxWidth - Ancho máximo (default 2000)
 * @param {number} quality - Calidad JPEG (0-1, default 0.8)
 */
export const compressImage = async (file, maxWidth = 2000, quality = 0.8) => {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;

                if (width > maxWidth) {
                    height = (height * maxWidth) / width;
                    width = maxWidth;
                }

                canvas.width = width;
                canvas.height = height;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(
                    (blob) => resolve(blob),
                    file.type || 'image/jpeg',
                    quality
                );
            };
            img.onerror = () => reject(new Error('Failed to load image'));
            img.src = e.target.result;
        };

        reader.onerror = () => reject(new Error('Failed to read file'));
        reader.readAsDataURL(file);
    });
};

export default {
    saveImageToIndexedDB,
    getImageFromIndexedDB,
    deleteImageFromIndexedDB,
    clearAllCartImages,
    getIndexedDBStorageSize,
    validateImageSize,
    compressImage
};
