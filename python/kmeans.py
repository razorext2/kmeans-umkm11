#!/usr/bin/env python3
"""
K-Means Clustering Script

This script performs K-means clustering on UMKM data.
It expects a JSON file containing 'data' and 'iterations' parameters.
"""

import sys
import json
import logging
import numpy as np
from sklearn.cluster import KMeans
from typing import Dict, List, Any, Tuple

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

def load_data(file_path: str) -> Dict[str, Any]:
    """Load and validate input JSON data."""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            payload = json.load(f)
        
        # Validate required fields
        if 'data' not in payload or 'iterations' not in payload or 'clusters' not in payload:
            raise ValueError("Missing required fields in input data. 'data', 'iterations', and 'clusters' are required.")
            
        if not isinstance(payload['data'], list) or len(payload['data']) == 0:
            raise ValueError("Data must be a non-empty list")
            
        if not isinstance(payload['iterations'], int) or payload['iterations'] <= 0:
            raise ValueError("Iterations must be a positive integer")
            
        if not isinstance(payload['clusters'], int) or payload['clusters'] < 2:
            raise ValueError("Number of clusters must be an integer >= 2")
            
        return payload
        
    except json.JSONDecodeError as e:
        logger.error(f"Invalid JSON: {str(e)}")
        raise
    except FileNotFoundError:
        logger.error(f"Input file not found: {file_path}")
        raise
    except Exception as e:
        logger.error(f"Error loading data: {str(e)}")
        raise

def validate_data_items(data: List[Dict]) -> None:
    """Validate data items structure."""
    required_fields = {'id', 'modal', 'penghasilan'}
    for i, item in enumerate(data):
        if not all(field in item for field in required_fields):
            raise ValueError(f"Missing required fields in item {i}. Required: {required_fields}")
        
        try:
            modal = float(item['modal'])
            penghasilan = float(item['penghasilan'])
            if modal < 0 or penghasilan < 0:
                raise ValueError(f"Negative values not allowed in item {i}")
        except (ValueError, TypeError) as e:
            raise ValueError(f"Invalid numeric value in item {i}: {str(e)}")

def perform_clustering(data: List[Dict], n_clusters: int, max_iter: int) -> Tuple[np.ndarray, np.ndarray]:
    """Perform K-means clustering on the data."""
    try:
        # Prepare data
        X = np.array([[float(item['modal']), float(item['penghasilan'])] for item in data])
        
        # Initialize and fit K-means
        kmeans = KMeans(
            n_clusters=n_clusters,
            init='k-means++',
            max_iter=max_iter,
            n_init=10,
            random_state=42  # For reproducibility
        )
        
        kmeans.fit(X)
        return kmeans.labels_, kmeans.cluster_centers_
        
    except Exception as e:
        logger.error(f"Error during clustering: {str(e)}")
        raise

def format_results(data: List[Dict], labels: np.ndarray, centroids: np.ndarray) -> Dict[str, Any]:
    """Format the clustering results into the required output format."""
    try:
        # Format labels
        formatted_labels = [
            {
                'id': int(item['id']),
                'cluster': int(label)
            }
            for item, label in zip(data, labels)
        ]
        
        # Format centroids
        formatted_centroids = [
            {
                'cluster_number': int(i),
                'centroid_modal': float(centroid[0]),
                'centroid_penghasilan': float(centroid[1])
            }
            for i, centroid in enumerate(centroids)
        ]
        
        return {
            'success': True,
            'labels': formatted_labels,
            'centroids': formatted_centroids
        }
        
    except Exception as e:
        logger.error(f"Error formatting results: {str(e)}")
        raise

def main():
    try:
        # Check command line arguments
        if len(sys.argv) != 2:
            print(json.dumps({
                'success': False,
                'error': 'Usage: python kmeans.py <input_file>'
            }), file=sys.stderr)
            sys.exit(1)
            
        # Load and validate input
        input_file = sys.argv[1]
        payload = load_data(input_file)
        validate_data_items(payload['data'])
        
        # Perform clustering
        labels, centroids = perform_clustering(
            data=payload['data'],
            n_clusters=payload['clusters'],
            max_iter=payload['iterations']
        )
        
        # Format and output results
        result = format_results(payload['data'], labels, centroids,)
        print(json.dumps(result, indent=2))
        
    except Exception as e:
        error_msg = f"Error: {str(e)}"
        logger.error(error_msg)
        print(json.dumps({
            'success': False,
            'error': error_msg
        }), file=sys.stderr)
        sys.exit(1)

if __name__ == "__main__":
    main()
