import tensorflow as tf
from tensorflow import keras
from tensorflow.keras import layers

# Caminhos das pastas
train_dir = "dataset/train"
test_dir = "dataset/test"

# Carregar datasets
# Carregar datasets
raw_train_ds = tf.keras.utils.image_dataset_from_directory(
    train_dir,
    image_size=(180, 180),
    batch_size=32
)

raw_test_ds = tf.keras.utils.image_dataset_from_directory(
    test_dir,
    image_size=(180, 180),
    batch_size=32
)

# Pegar classes antes de otimizar
class_names = raw_train_ds.class_names
num_classes = len(class_names)
print("Classes detectadas:", class_names)

# Otimizar leitura
AUTOTUNE = tf.data.AUTOTUNE
train_ds = raw_train_ds.cache().shuffle(1000).prefetch(buffer_size=AUTOTUNE)
test_ds = raw_test_ds.cache().prefetch(buffer_size=AUTOTUNE)
model = keras.Sequential([
    layers.Rescaling(1./255, input_shape=(180, 180, 3)),
    layers.Conv2D(16, 3, padding="same", activation="relu"),
    layers.MaxPooling2D(),
    layers.Conv2D(32, 3, padding="same", activation="relu"),
    layers.MaxPooling2D(),
    layers.Conv2D(64, 3, padding="same", activation="relu"),
    layers.MaxPooling2D(),
    layers.Flatten(),
    layers.Dense(128, activation="relu"),
    layers.Dense(num_classes)
])

# Compilar modelo
model.compile(
    optimizer="adam",
    loss=tf.keras.losses.SparseCategoricalCrossentropy(from_logits=True),
    metrics=["accuracy"]
)

# Treinar
epochs = 20
history = model.fit(
    train_ds,
    validation_data=test_ds,
    epochs=epochs
)

# Salvar modelo
model.save("modelo_planta.keras")

print("Treinamento finalizado e modelo salvo!")
with open("classes.txt", "w") as f:
    for class_name in class_names:  # usamos a lista salva antes
        f.write(class_name + "\n")
