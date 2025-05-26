import sys
import json
import logging
import numpy as np
from typing import List, Dict, Any

# Logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

def load_data(file_path: str) -> Dict[str, Any]:
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            payload = json.load(f)

        if 'data' not in payload or 'iterations' not in payload or 'clusters' not in payload:
            raise ValueError("Missing required fields: 'data', 'iterations', 'clusters'.")

        if not isinstance(payload['data'], list) or len(payload['data']) == 0:
            raise ValueError("Data must be a non-empty list.")

        if not isinstance(payload['iterations'], int) or payload['iterations'] <= 0:
            raise ValueError("Iterations must be a positive integer.")

        if not isinstance(payload['clusters'], int) or payload['clusters'] < 2:
            raise ValueError("Clusters must be at least 2.")

        return payload
    except Exception as e:
        logger.error(f"Error loading data: {e}")
        sys.exit(1)

def manual_kmeans(data: List[Dict], n_clusters: int, max_iter: int) -> Dict[str, Any]:
    X = np.array([[float(d['modal']), float(d['penghasilan'])] for d in data])
    ids = [d['id'] for d in data]
    n_samples = len(data)

    np.random.seed(42)
    initial_idx = np.random.choice(n_samples, size=n_clusters, replace=False)
    centroids = X[initial_idx]

    history = []

    for iteration in range(1, max_iter + 1):
        distances = np.zeros((n_samples, n_clusters))
        for i in range(n_clusters):
            distances[:, i] = np.linalg.norm(X - centroids[i], axis=1)

        labels = np.argmin(distances, axis=1)

        iter_data = {
            'iteration': iteration,
            'centroids': centroids.tolist(),
            'points': []
        }

        for idx in range(n_samples):
            iter_data['points'].append({
                'umkm_id': ids[idx],
                'distances': distances[idx].tolist(),
                'assigned_cluster': int(labels[idx])
            })

        history.append(iter_data)

        # Update centroids
        new_centroids = np.array([
            X[labels == i].mean(axis=0) if np.any(labels == i) else centroids[i]
            for i in range(n_clusters)
        ])

        if np.allclose(centroids, new_centroids):
            break

        centroids = new_centroids

    return {
        'success': True,
        'history': history,
        'final_labels': [
            {'id': ids[i], 'cluster': int(labels[i])}
            for i in range(n_samples)
        ],
        'final_centroids': [
            {
                'cluster_number': i,
                'centroid_modal': float(centroid[0]),
                'centroid_penghasilan': float(centroid[1])
            } for i, centroid in enumerate(centroids)
        ]
    }

def main():
    if len(sys.argv) != 2:
        print(json.dumps({
            'success': False,
            'error': 'Usage: python kmeans.py <input_file>'
        }), file=sys.stderr)
        sys.exit(1)

    input_file = sys.argv[1]
    payload = load_data(input_file)

    try:
        result = manual_kmeans(
            data=payload['data'],
            n_clusters=payload['clusters'],
            max_iter=payload['iterations']
        )
        print(json.dumps(result, indent=2))
    except Exception as e:
        logger.error(f"Clustering failed: {e}")
        print(json.dumps({
            'success': False,
            'error': str(e)
        }), file=sys.stderr)
        sys.exit(1)

if __name__ == '__main__':
    main()
