import tensorflow as tf
import os

# Configurações
IMG_SIZE = 224   # Tamanho para redimensionar as imagens
BATCH_SIZE = 32  # Quantas imagens por lote

# Caminho para os dados
train_dir = os.path.join("dataset", "train")
test_dir = os.path.join("dataset", "test")

# Função para carregar dataset
def carregar_dados():
    # Carregar dataset de treino
    train_ds = tf.keras.utils.image_dataset_from_directory(
        train_dir,
        image_size=(IMG_SIZE, IMG_SIZE),
        batch_size=BATCH_SIZE,
        label_mode="categorical"  # Para classificação multi-classe
    )

    # Carregar dataset de teste
    test_ds = tf.keras.utils.image_dataset_from_directory(
        test_dir,
        image_size=(IMG_SIZE, IMG_SIZE),
        batch_size=BATCH_SIZE,
        label_mode="categorical"
    )

    # Normalizar os valores para 0-1
    normalization_layer = tf.keras.layers.Rescaling(1./255)

    train_ds = train_ds.map(lambda x, y: (normalization_layer(x), y))
    test_ds = test_ds.map(lambda x, y: (normalization_layer(x), y))

    # Otimizar carregamento
    train_ds = train_ds.cache().shuffle(1000).prefetch(buffer_size=tf.data.AUTOTUNE)
    test_ds = test_ds.cache().prefetch(buffer_size=tf.data.AUTOTUNE)

    return train_ds, test_ds

if __name__ == "__main__":
    train_ds, test_ds = carregar_dados()
    class_names = train_ds.class_names
    print(f"Classes detectadas: {class_names}")
    print(f"Total de classes: {len(class_names)}")
